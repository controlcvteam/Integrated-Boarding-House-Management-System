<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant) {
            return redirect()->route('tenant.dashboard');
        }

        $payments = Payment::where('tenant_id', $tenant->id)
            ->with('room')
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $totalPaid = Payment::where('tenant_id', $tenant->id)
            ->whereIn('status', ['paid', 'verified', 'partial'])
            ->sum('amount');

        $pendingPaymentsCount = Payment::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->count();

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $currentPayment = Payment::where('tenant_id', $tenant->id)
            ->where('billing_month', $currentMonth)
            ->where('billing_year', $currentYear)
            ->latest()
            ->first();

        return view('tenant.payments.index', compact('tenant', 'payments', 'totalPaid', 'pendingPaymentsCount', 'currentPayment', 'currentMonth', 'currentYear'));
    }

    public function submitGcashForm(Request $request)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant || !$tenant->room) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'You must have an assigned room to submit rent payments.');
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $monthlyRent = $tenant->room->monthly_rent;

        $gcashName = Setting::get('gcash_name', 'Admin');
        $gcashNumber = Setting::get('gcash_number', '0917-888-9999');
        $gcashQrPath = Setting::get('gcash_qr_path', 'settings/default-gcash-qr.svg');

        $isGcashOnly = $request->query('method') === 'gcash';

        return view('tenant.payments.submit-gcash', compact('tenant', 'currentMonth', 'currentYear', 'monthlyRent', 'gcashName', 'gcashNumber', 'gcashQrPath', 'isGcashOnly'));
    }

    public function storeGcash(Request $request)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant || !$tenant->room) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'You must have an assigned room to submit rent payments.');
        }

        $paymentMethod = $request->input('payment_method', 'gcash');

        $rules = [
            'payment_method' => 'required|in:gcash,cash',
            'billing_month' => 'required|integer|min:1|max:12',
            'billing_year' => 'required|integer|min:2020|max:2050',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_time' => 'nullable',
            'notes' => 'nullable|string|max:500',
        ];

        if ($paymentMethod === 'gcash') {
            $rules['gcash_reference'] = 'required|string|max:100';
            $rules['receipt'] = 'required|image|mimes:jpeg,png,jpg,webp|max:5120';
        } else {
            // Cash payment: no proof or reference required, but landlord must verify
            $rules['gcash_reference'] = 'nullable|string|max:100';
            $rules['receipt'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
        }

        $validated = $request->validate($rules);

        // Upload receipt screenshot if provided
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = FileUploadService::store($request->file('receipt'), 'receipts');
        }

        // Generate payment code
        $nextId = (int) Payment::max('id') + 1;
        do {
            $paymentCode = 'PAY-' . date('Y') . '-' . str_pad($nextId++, 5, '0', STR_PAD_LEFT);
        } while (Payment::where('payment_code', $paymentCode)->exists());

        $paymentTimeVal = $request->filled('payment_time') ? $request->payment_time : now()->format('H:i:s');
        if (strlen($paymentTimeVal) === 5) {
            $paymentTimeVal .= ':00';
        }

        $payment = Payment::create([
            'payment_code' => $paymentCode,
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'amount' => $validated['amount'],
            'billing_month' => $validated['billing_month'],
            'billing_year' => $validated['billing_year'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'payment_time' => $paymentTimeVal,
            'gcash_reference' => $validated['gcash_reference'] ?? null,
            'receipt_path' => $receiptPath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending', // Strictly pending verification by landlord
            'is_edited' => false,
        ]);

        // Notify Landlord/Admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $monthName = \DateTime::createFromFormat('!m', $validated['billing_month'])->format('F');
            $methodLabel = $validated['payment_method'] === 'cash' ? 'Cash' : 'GCash';
            $refText = !empty($validated['gcash_reference']) ? " (Ref: {$validated['gcash_reference']})" : '';

            AppNotification::send(
                $admin->id,
                "[PENDING] New {$methodLabel} Payment Submitted",
                "Tenant {$tenant->user->name} recorded a {$methodLabel} payment of ₱" . number_format($validated['amount'], 2) . " for {$monthName} {$validated['billing_year']}{$refText} awaiting landlord verification.",
                route('admin.payments.show', $payment->id),
                'payment'
            );
        }

        if ($validated['payment_method'] === 'cash') {
            return redirect()->route('tenant.payments.index')
                ->with('success', '[SUCCESS] Your Cash payment notice of ₱' . number_format($validated['amount'], 2) . ' has been recorded! No receipt proof is needed. The landlord will verify and confirm once collected.');
        }

        return redirect()->route('tenant.payments.index')
            ->with('success', '[SUCCESS] Your GCash payment proof has been submitted successfully! The Landlord will verify your transaction shortly.');
    }

    public function showReceipt($id)
    {
        $tenant = auth()->user()->tenant;
        $payment = Payment::where('tenant_id', $tenant->id)
            ->with(['room', 'tenant.user', 'editHistories.user'])
            ->findOrFail($id);

        if ($payment->status === 'pending') {
            return redirect()->route('tenant.payments.index')
                ->with('info', 'Payment #' . ($payment->payment_code ?? ('PAY-' . $payment->id)) . ' is currently pending landlord verification. The official receipt will be generated and accessible once verified.');
        }

        return view('tenant.payments.receipt', compact('payment'));
    }


    public function editHistoryData($id)
    {
        $user = auth()->user();
        if ($user && $user->role === 'admin') {
            $payment = Payment::with(['tenant.user', 'room', 'editHistories.user'])->findOrFail($id);
        } else {
            $tenant = $user ? $user->tenant : null;
            if (!$tenant) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
            $payment = Payment::where('tenant_id', $tenant->id)
                ->with(['tenant.user', 'room', 'editHistories.user'])
                ->findOrFail($id);
        }

        $histories = $payment->editHistories;
        $totalEdits = $histories->count();

        // Earliest history has original values snapshot
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
            'date' => isset($originalSnapshot['payment_date']) ? \Carbon\Carbon::parse($originalSnapshot['payment_date'])->format('M d, Y') : ($payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A'),
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
                'rental_period' => $payment->billing_period_label,
                'payment_method' => strtoupper($payment->payment_method),
                'payment_date' => $payment->payment_date ? $payment->payment_date->format('F d, Y') : 'N/A',
                'status' => ucfirst($payment->status),
                'receipt_number' => '#REC-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT),
                'receipt_url' => $payment->receipt_url,
                'is_edited' => (bool)$payment->is_edited,
            ],
            'original_record' => $originalRecord,
            'total_edits' => $totalEdits,
            'histories' => $historyItems,
        ]);
    }

    public function historyPage($id)
    {
        $user = auth()->user();
        if ($user && $user->role === 'admin') {
            $payment = Payment::with(['tenant.user', 'room', 'editHistories.user'])->findOrFail($id);
        } else {
            $tenant = $user ? $user->tenant : null;
            if (!$tenant) {
                return redirect()->route('tenant.dashboard');
            }
            $payment = Payment::where('tenant_id', $tenant->id)
                ->with(['tenant.user', 'room', 'editHistories.user'])
                ->findOrFail($id);
        }

        $histories = $payment->editHistories;
        $earliest = $payment->earliest_edit_history;
        $originalSnapshot = $earliest ? ($earliest->old_values ?? []) : [];

        return view('tenant.payments.history', compact('payment', 'histories', 'originalSnapshot'));
    }
}

