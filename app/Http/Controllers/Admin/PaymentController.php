<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Payment;
use App\Models\PaymentEditHistory;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['tenant.user', 'room', 'receiver', 'verifier', 'editHistories']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_code', 'like', "%{$search}%")
                  ->orWhere('gcash_reference', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($tq) use ($search) {
                      $tq->where('full_name', 'like', "%{$search}%");
                  })->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->whereIn('status', ['paid', 'verified']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('month')) {
            $query->where('billing_month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('billing_year', $request->year);
        }

        $payments = $query->latest('payment_date')->paginate(10)->withQueryString();

        $statusCounts = Payment::selectRaw("status, count(*) as total")->groupBy('status')->pluck('total', 'status');
        $paymentCounts = [
            'all' => (int) $statusCounts->sum(),
            'pending' => (int) $statusCounts->get('pending', 0),
            'paid' => (int) ($statusCounts->get('paid', 0) + $statusCounts->get('verified', 0)),
            'partial' => (int) $statusCounts->get('partial', 0),
            'rejected' => (int) $statusCounts->get('rejected', 0),
        ];

        return view('admin.payments.index', compact('payments', 'paymentCounts'));
    }

    public function create(Request $request)
    {
        $tenants = Tenant::with(['room', 'payments'])->orderBy('full_name')->get();
        $selectedTenantId = $request->tenant_id;
        $selectedTenant = $selectedTenantId ? Tenant::with(['room', 'payments'])->find($selectedTenantId) : null;

        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        // Group tenants by current payment category:
        // 1. Who Pay (Unpaid / Rent Due)
        // 2. Pending Verification
        // 3. Partial
        // 4. Rejected
        // 5. Paid
        // 6. Other / Inactive
        $groupedTenants = [
            'who_pay' => collect(),
            'pending' => collect(),
            'partial' => collect(),
            'rejected' => collect(),
            'paid' => collect(),
            'other' => collect(),
        ];

        foreach ($tenants as $t) {
            if (!$t->isActive() || !$t->room) {
                $groupedTenants['other']->push($t);
                continue;
            }

            $category = $t->getPaymentStatusCategory($currentMonth, $currentYear);
            if (isset($groupedTenants[$category])) {
                $groupedTenants[$category]->push($t);
            } else {
                $groupedTenants['who_pay']->push($t);
            }
        }

        return view('admin.payments.create', compact('tenants', 'groupedTenants', 'selectedTenant', 'currentYear', 'currentMonth'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'billing_month' => ['required', 'integer', 'between:1,12'],
            'billing_year' => ['required', 'integer', 'min:2020', 'max:2050'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,gcash'],
            'payment_date' => ['required', 'date'],
            'payment_time' => ['nullable'],
            'gcash_reference' => ['nullable', 'string', 'max:100'],
            'receipt' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf', 'max:5120'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ], [
            'payment_method.required' => 'The payment method field is required.',
        ]);

        $tenant = Tenant::with('room')->findOrFail($validated['tenant_id']);
        $room = $tenant->room;

        if (!$room) {
            return back()->withInput()->with('error', 'Selected tenant is not currently assigned to any room.');
        }

        // Duplicate payment protection (Section 54)
        $existingPaidSum = Payment::where('tenant_id', $tenant->id)
            ->where('billing_month', $validated['billing_month'])
            ->where('billing_year', $validated['billing_year'])
            ->whereIn('status', ['paid', 'verified', 'partial'])
            ->sum('amount');

        if ($existingPaidSum >= $room->monthly_rent && $room->monthly_rent > 0) {
            return back()->withInput()->with('error', 'Rent for ' . Carbon::createFromDate($validated['billing_year'], $validated['billing_month'], 1)->format('F Y') . ' is already fully paid.');
        }

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        // Determine status based on amount vs monthly rent
        $totalPaid = $existingPaidSum + (float)$validated['amount'];
        $status = ($totalPaid >= (float)$room->monthly_rent) ? 'verified' : 'partial';

        // Payment code generation
        $nextId = (int) Payment::max('id') + 1;
        do {
            $paymentCode = 'PAY-' . date('Y') . '-' . str_pad($nextId++, 5, '0', STR_PAD_LEFT);
        } while (Payment::where('payment_code', $paymentCode)->exists());

        $paymentTime = $request->filled('payment_time') ? $request->payment_time : now()->format('H:i:s');

        $payment = Payment::create([
            'payment_code' => $paymentCode,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => $validated['billing_month'],
            'billing_year' => $validated['billing_year'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'payment_time' => $paymentTime,
            'gcash_reference' => $validated['gcash_reference'] ?? null,
            'receipt_path' => $receiptPath,
            'status' => $status,
            'remarks' => $validated['remarks'] ?? null,
            'received_by' => auth()->id(),
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        // Send notification to tenant
        $monthName = Carbon::createFromDate($validated['billing_year'], $validated['billing_month'], 1)->format('F Y');
        $statusWord = ($status === 'verified') ? 'Fully Paid' : 'Partially Paid';
        $notifTitle = $validated['payment_method'] === 'cash' ? 'Cash Payment Succesful' : 'Gcash Payment Succesful';
        $flashMsg = $validated['payment_method'] === 'cash'
            ? "Cash Payment Succesful: ₱" . number_format($payment->amount, 2) . " for {$tenant->full_name} ({$monthName}) has been recorded!"
            : "Gcash Payment Succesful: ₱" . number_format($payment->amount, 2) . " for {$tenant->full_name} ({$monthName}) has been recorded!";
        
        AppNotification::create([
            'user_id' => $tenant->user_id,
            'title' => $notifTitle,
            'message' => "{$notifTitle}: Your payment of ₱" . number_format($payment->amount, 2) . " for {$monthName} has been officially recorded as {$statusWord}.",
            'type' => 'success',
            'link' => route('tenant.payments.index'),
            'is_read' => false,
        ]);

        return redirect()->route('admin.payments.show', $payment->id)
            ->with('success', $flashMsg);
    }

    public function show($id)
    {
        $payment = ($id instanceof Payment && $id->exists) ? $id : Payment::findOrFail($id);
        $payment->load(['tenant.user', 'room', 'receiver', 'verifier', 'editHistories.user']);
        return view('admin.payments.show', compact('payment'));
    }

    public function edit($id)
    {
        $payment = ($id instanceof Payment && $id->exists) ? $id : Payment::findOrFail($id);
        $payment->load(['tenant', 'room', 'editHistories.user']);
        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, $id)
    {
        $payment = ($id instanceof Payment && $id->exists) ? $id : Payment::findOrFail($id);
        
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'billing_month' => ['required', 'integer', 'between:1,12'],
            'billing_year' => ['required', 'integer', 'min:2020', 'max:2050'],
            'payment_date' => ['required', 'date'],
            'payment_time' => ['nullable'],
            'payment_method' => ['required', 'in:cash,gcash'],
            'status' => ['required', 'in:paid,pending,partial,rejected,verified'],
            'edit_reason' => ['required', 'string', 'min:3', 'max:1000'],
            'gcash_reference' => ['nullable', 'string', 'max:100'],
            'receipt' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf', 'max:5120'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ], [
            'edit_reason.required' => 'A reason for editing this payment record is mandatory for audit logging.',
            'edit_reason.min' => 'Please provide a clear reason for this edit (at least 3 characters).',
        ]);

        $receiptPath = $payment->receipt_path;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        // Capture previous values snapshot before update
        $oldMonthName = Carbon::createFromDate($payment->billing_year, $payment->billing_month, 1)->format('F Y');
        $newMonthName = Carbon::createFromDate($validated['billing_year'], $validated['billing_month'], 1)->format('F Y');

        $paymentTimeVal = $request->filled('payment_time') ? $request->payment_time : ($payment->payment_time ?: now()->format('H:i:s'));
        if (strlen($paymentTimeVal) === 5) {
            $paymentTimeVal .= ':00';
        }

        $oldValues = [
            'amount' => (float) $payment->amount,
            'billing_month' => (int) $payment->billing_month,
            'billing_year' => (int) $payment->billing_year,
            'rental_period' => $oldMonthName,
            'payment_method' => $payment->payment_method,
            'payment_date' => $payment->payment_date ? $payment->payment_date->format('Y-m-d') : null,
            'payment_time' => $payment->payment_time ? Carbon::parse($payment->payment_time)->format('H:i:s') : null,
            'status' => $payment->status,
            'gcash_reference' => $payment->gcash_reference,
            'remarks' => $payment->remarks,
        ];

        $newValues = [
            'amount' => (float) $validated['amount'],
            'billing_month' => (int) $validated['billing_month'],
            'billing_year' => (int) $validated['billing_year'],
            'rental_period' => $newMonthName,
            'payment_method' => $validated['payment_method'],
            'payment_date' => Carbon::parse($validated['payment_date'])->format('Y-m-d'),
            'payment_time' => $paymentTimeVal,
            'status' => $validated['status'],
            'gcash_reference' => $validated['gcash_reference'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
        ];

        // Determine which fields actually changed
        $changedFields = [];
        if (abs((float)$oldValues['amount'] - (float)$newValues['amount']) > 0.001) {
            $changedFields['amount'] = [
                'field_label' => 'Amount',
                'old' => '₱' . number_format($oldValues['amount'], 2),
                'new' => '₱' . number_format($newValues['amount'], 2),
            ];
        }

        if ($oldValues['billing_month'] !== $newValues['billing_month'] || $oldValues['billing_year'] !== $newValues['billing_year']) {
            $changedFields['rental_period'] = [
                'field_label' => 'Rental Period',
                'old' => $oldValues['rental_period'],
                'new' => $newValues['rental_period'],
            ];
        }

        if (strtolower($oldValues['payment_method']) !== strtolower($newValues['payment_method'])) {
            $changedFields['payment_method'] = [
                'field_label' => 'Payment Method',
                'old' => strtoupper($oldValues['payment_method']),
                'new' => strtoupper($newValues['payment_method']),
            ];
        }

        if ($oldValues['payment_date'] !== $newValues['payment_date']) {
            $changedFields['payment_date'] = [
                'field_label' => 'Payment Date',
                'old' => $oldValues['payment_date'] ? Carbon::parse($oldValues['payment_date'])->format('M d, Y') : 'N/A',
                'new' => Carbon::parse($newValues['payment_date'])->format('M d, Y'),
            ];
        }

        if ($oldValues['payment_time'] !== $newValues['payment_time']) {
            $changedFields['payment_time'] = [
                'field_label' => 'Payment Time',
                'old' => $oldValues['payment_time'] ? Carbon::parse($oldValues['payment_time'])->format('g:i A') : 'N/A',
                'new' => $newValues['payment_time'] ? Carbon::parse($newValues['payment_time'])->format('g:i A') : 'N/A',
            ];
        }

        if (strtolower($oldValues['status']) !== strtolower($newValues['status'])) {
            $changedFields['status'] = [
                'field_label' => 'Status',
                'old' => ucfirst($oldValues['status']),
                'new' => ucfirst($newValues['status']),
            ];
        }

        if (($oldValues['gcash_reference'] ?? '') !== ($newValues['gcash_reference'] ?? '')) {
            $changedFields['gcash_reference'] = [
                'field_label' => 'Reference Number',
                'old' => $oldValues['gcash_reference'] ?: 'None',
                'new' => $newValues['gcash_reference'] ?: 'None',
            ];
        }

        if (($oldValues['remarks'] ?? '') !== ($newValues['remarks'] ?? '')) {
            $changedFields['remarks'] = [
                'field_label' => 'Remarks',
                'old' => $oldValues['remarks'] ?: 'None',
                'new' => $newValues['remarks'] ?: 'None',
            ];
        }

        if ($request->hasFile('receipt')) {
            $changedFields['receipt'] = [
                'field_label' => 'Payment Receipt / Proof',
                'old' => $payment->receipt_path ? 'Previous receipt attachment' : 'No previous attachment',
                'new' => 'Updated receipt attachment uploaded',
            ];
        }

        // Record edit history (never overwritten, never deleted)
        PaymentEditHistory::create([
            'payment_id' => $payment->id,
            'user_id' => auth()->id(),
            'editor_name' => auth()->user() ? auth()->user()->name : 'Landlord',
            'reason' => $validated['edit_reason'],
            'changed_fields' => $changedFields,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);

        // Automatically mark payment as edited
        $payment->update([
            'amount' => $validated['amount'],
            'billing_month' => $validated['billing_month'],
            'billing_year' => $validated['billing_year'],
            'payment_date' => $validated['payment_date'],
            'payment_time' => $newValues['payment_time'],
            'payment_method' => $validated['payment_method'],
            'gcash_reference' => $validated['gcash_reference'] ?? null,
            'receipt_path' => $receiptPath,
            'status' => $validated['status'],
            'remarks' => $validated['remarks'] ?? null,
            'is_edited' => true,
        ]);

        return redirect()->route('admin.payments.show', $payment->id)
            ->with('success', 'Payment record #' . $payment->payment_code . ' updated successfully! Edit recorded into permanent audit history with EDITED indicator.');
    }

    public function destroy($id)
    {
        // Protected: Payment records and audit history must never be permanently deleted
        return redirect()->route('admin.payments.index')
            ->with('error', 'Payment records cannot be deleted to preserve financial accountability and accounting history. Please use Edit to correct any encoding errors.');
    }

    public function editHistoryData($id)
    {
        $payment = Payment::with(['tenant.user', 'room', 'editHistories.user'])->findOrFail($id);

        $histories = $payment->editHistories;
        $totalEdits = $histories->count();

        // Earliest history has original values
        $earliest = $payment->earliest_edit_history;
        $originalSnapshot = $earliest ? ($earliest->old_values ?? []) : [];

        $historyItems = [];
        foreach ($histories as $idx => $h) {
            $editNumber = $totalEdits - $idx; // e.g. Edit #3, Edit #2, Edit #1
            $historyItems[] = [
                'id' => $h->id,
                'edit_number' => $editNumber,
                'editor_name' => $h->editor_display_name,
                'date_formatted' => $h->created_at->format('F j, Y — g:i A'),
                'reason' => $h->reason,
                'changed_fields' => $h->changed_fields ?: [],
                'old_values' => $h->old_values ?: [],
                'new_values' => $h->new_values ?: [],
            ];
        }

        // Original record info
        $originalRecord = [
            'amount' => isset($originalSnapshot['amount']) ? '₱' . number_format($originalSnapshot['amount'], 2) : ('₱' . number_format($payment->amount, 2)),
            'payment_method' => isset($originalSnapshot['payment_method']) ? strtoupper($originalSnapshot['payment_method']) : strtoupper($payment->payment_method),
            'rental_period' => $originalSnapshot['rental_period'] ?? $payment->billing_period_label,
            'date' => isset($originalSnapshot['payment_date']) ? Carbon::parse($originalSnapshot['payment_date'])->format('M d, Y') : ($payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A'),
            'status' => isset($originalSnapshot['status']) ? ucfirst($originalSnapshot['status']) : ucfirst($payment->status),
            'created_at' => $payment->created_at->format('F j, Y — g:i A'),
        ];

        return response()->json([
            'payment' => [
                'id' => $payment->id,
                'payment_code' => $payment->payment_code ?: ('PAY-' . $payment->id),
                'tenant_name' => $payment->tenant ? $payment->tenant->full_name : 'N/A',
                'room_number' => $payment->room ? 'Room ' . $payment->room->room_number : 'Unassigned',
                'room_type' => $payment->room ? $payment->room->room_type : '',
                'amount' => '₱' . number_format($payment->amount, 2),
                'amount_raw' => (float)$payment->amount,
                'rental_period' => $payment->billing_period_label,
                'payment_method' => strtoupper($payment->payment_method),
                'payment_date' => $payment->payment_date ? $payment->payment_date->format('F d, Y') : 'N/A',
                'status' => ucfirst($payment->status),
                'receipt_number' => '#REC-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT),
                'receipt_url' => $payment->receipt_url,
                'is_edited' => (bool)$payment->is_edited,
            ],
            'original_record' => $originalRecord,
            'histories' => $historyItems,
            'total_edits' => $totalEdits,
        ]);
    }

    public function historyPage($id)
    {
        $payment = Payment::with(['tenant.user', 'room', 'editHistories.user'])->findOrFail($id);
        return view('admin.payments.history', compact('payment'));
    }

    public function verifyGcash(Request $request, $id)
    {
        return $this->approveGcash($request, $id);
    }

    public function approveGcash(Request $request, $id)
    {
        $payment = $id instanceof Payment ? $id : Payment::findOrFail($id);
        $room = $payment->room;
        $existingPaidSum = Payment::where('tenant_id', $payment->tenant_id)
            ->where('billing_month', $payment->billing_month)
            ->where('billing_year', $payment->billing_year)
            ->whereIn('status', ['paid', 'verified', 'partial'])
            ->where('id', '!=', $payment->id)
            ->sum('amount');

        $total = $existingPaidSum + $payment->amount;
        $status = ($room && $total >= $room->monthly_rent) ? 'verified' : 'partial';

        $payment->update([
            'status' => $status,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        $isCash = strtolower($payment->payment_method) === 'cash';
        $notifTitle = $isCash ? 'Cash Payment Succesful' : 'Gcash Payment Succesful';
        $methodLabel = $isCash ? 'Cash' : 'GCash';
        $refText = (!$isCash && $payment->gcash_reference) ? ' (Ref: ' . $payment->gcash_reference . ')' : '';

        AppNotification::create([
            'user_id' => $payment->tenant->user_id,
            'title' => $notifTitle,
            'message' => "{$notifTitle}: Your {$methodLabel} payment of ₱" . number_format($payment->amount, 2) . " for " . $payment->billing_period_label . "{$refText} was verified and marked as Paid.",
            'type' => 'success',
            'link' => route('tenant.payments.index'),
            'action_url' => route('tenant.payments.index'),
            'is_read' => false,
        ]);

        $flashMsg = "{$notifTitle}: {$methodLabel} payment of ₱" . number_format($payment->amount, 2) . " for " . ($payment->tenant ? $payment->tenant->full_name : 'tenant') . " has been verified and marked as Paid!";

        return back()->with('success', $flashMsg);
    }

    public function rejectGcash(Request $request, $id)
    {
        $payment = $id instanceof Payment ? $id : Payment::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $payment->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $isCash = strtolower($payment->payment_method) === 'cash';
        $methodLabel = $isCash ? 'Cash' : 'GCash';

        AppNotification::create([
            'user_id' => $payment->tenant->user_id,
            'title' => "[FAILED] {$methodLabel} Payment Rejected",
            'message' => "Your {$methodLabel} payment for " . $payment->billing_period_label . " was rejected. Reason: " . $validated['rejection_reason'] . ".",
            'type' => 'danger',
            'link' => route('tenant.payments.index'),
            'is_read' => false,
        ]);

        return back()->with('error', "[FAILED] {$methodLabel} payment verification failed. Rejection recorded: " . $validated['rejection_reason']);
    }

    public function recordCashQuick(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'billing_month' => ['required', 'integer', 'between:1,12'],
            'billing_year' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,gcash'],
            'payment_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ], [
            'payment_method.required' => 'The payment method field is required.',
        ]);

        $tenant = Tenant::with('room')->findOrFail($validated['tenant_id']);
        $room = $tenant->room;

        if (!$room) {
            return back()->with('error', 'Tenant is not currently assigned to any room.');
        }

        $nextId = (int) Payment::max('id') + 1;
        do {
            $paymentCode = 'PAY-' . date('Y') . '-' . str_pad($nextId++, 5, '0', STR_PAD_LEFT);
        } while (Payment::where('payment_code', $paymentCode)->exists());

        $existingPaidSum = Payment::where('tenant_id', $tenant->id)
            ->where('billing_month', $validated['billing_month'])
            ->where('billing_year', $validated['billing_year'])
            ->whereIn('status', ['paid', 'verified', 'partial'])
            ->sum('amount');

        $totalPaid = $existingPaidSum + (float)$validated['amount'];
        $status = ($totalPaid >= (float)$room->monthly_rent) ? 'verified' : 'partial';

        Payment::create([
            'payment_code' => $paymentCode,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => $validated['billing_month'],
            'billing_year' => $validated['billing_year'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'status' => $status,
            'remarks' => $validated['remarks'] ?? 'Payment received directly by Admin',
            'received_by' => auth()->id(),
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $monthName = Carbon::createFromDate($validated['billing_year'], $validated['billing_month'], 1)->format('F Y');
        $notifTitle = $validated['payment_method'] === 'cash' ? 'Cash Payment Succesful' : 'Gcash Payment Succesful';
        $flashMsg = $validated['payment_method'] === 'cash'
            ? "Cash Payment Succesful: ₱" . number_format($validated['amount'], 2) . " for {$tenant->full_name} has been recorded!"
            : "Gcash Payment Succesful: ₱" . number_format($validated['amount'], 2) . " for {$tenant->full_name} has been recorded!";

        AppNotification::create([
            'user_id' => $tenant->user_id,
            'title' => $notifTitle,
            'message' => "{$notifTitle}: Payment of ₱" . number_format($validated['amount'], 2) . " for {$monthName} was received and marked as Paid by Admin.",
            'type' => 'success',
            'link' => route('tenant.payments.index'),
            'is_read' => false,
        ]);

        return back()->with('success', $flashMsg);
    }
}
