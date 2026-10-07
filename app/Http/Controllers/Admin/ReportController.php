<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'collections');
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $selectedDate = Carbon::createFromDate($year, $month, 1);
        $monthName = $selectedDate->format('F Y');

        $data = [
            'type' => $type,
            'month' => $month,
            'year' => $year,
            'monthName' => $monthName,
        ];

        switch ($type) {
            case 'collections':
                // Collections for specified month and year (includes verified, paid, and partial payments)
                $payments = Payment::with(['tenant.user', 'room'])
                    ->where('billing_month', $month)
                    ->where('billing_year', $year)
                    ->whereIn('status', ['paid', 'verified', 'partial'])
                    ->orderBy('payment_date', 'desc')
                    ->get();

                $totalCollected = $payments->sum('amount');
                $totalCash = $payments->where('payment_method', 'cash')->sum('amount');
                $totalGcash = $payments->where('payment_method', 'gcash')->sum('amount');
                $cashCount = $payments->where('payment_method', 'cash')->count();
                $gcashCount = $payments->where('payment_method', 'gcash')->count();

                $data = array_merge($data, compact(
                    'payments',
                    'totalCollected',
                    'totalCash',
                    'totalGcash',
                    'cashCount',
                    'gcashCount'
                ));
                break;

            case 'outstanding':
                // Active tenants who have remaining rent balance for this billing cycle
                $activeTenants = Tenant::with(['user', 'room'])->where('status', 'active')->get();

                $outstandingList = [];
                $totalOutstanding = 0;
                $overdueCount = 0;
                $today = now()->copy()->startOfDay();

                foreach ($activeTenants as $tenant) {
                    $rentStatus = $tenant->getRentStatusForMonthYear($month, $year);
                    $balance = (float) $rentStatus['balance'];

                    if ($balance > 0) {
                        $dueDate = $rentStatus['due_date'] ?? $tenant->calculateDueDateFor($month, $year);
                        $hasPending = $rentStatus['status'] === 'pending_verification';
                        $isOverdue = in_array($rentStatus['status'], ['overdue', 'due']) || ($dueDate && $dueDate->lt($today) && !$hasPending);

                        if ($isOverdue && !$hasPending) {
                            $overdueCount++;
                        }

                        $totalOutstanding += $balance;

                        $pendingPayment = Payment::where('tenant_id', $tenant->id)
                            ->where('billing_month', $month)
                            ->where('billing_year', $year)
                            ->where('status', 'pending')
                            ->latest()
                            ->first();

                        $outstandingList[] = [
                            'tenant' => $tenant,
                            'room' => $tenant->room,
                            'amount' => $balance,
                            'paid_amount' => $rentStatus['paid_amount'],
                            'monthly_rent' => $tenant->room ? $tenant->room->monthly_rent : 0,
                            'due_date' => $dueDate,
                            'is_overdue' => $isOverdue,
                            'status_label' => $rentStatus['label'],
                            'has_pending' => $hasPending,
                            'pending_payment' => $pendingPayment,
                        ];
                    }
                }

                $data = array_merge($data, compact('outstandingList', 'totalOutstanding', 'overdueCount'));
                break;

            case 'occupancy':
                $rooms = Room::with(['activeTenants.user'])->orderBy('room_number')->get();
                $totalRooms = $rooms->count();
                $totalCapacity = $rooms->sum('capacity');
                $occupiedSlots = $rooms->sum(fn($r) => $r->activeTenants->count());
                $availableSlots = max(0, $totalCapacity - $occupiedSlots);
                $occupancyRate = $totalCapacity > 0 ? round(($occupiedSlots / $totalCapacity) * 100, 1) : 0;

                $potentialMonthlyRevenue = $rooms->sum(function($room) {
                    return $room->monthly_rent * $room->capacity;
                });
                $currentMonthlyRevenue = $rooms->sum(function($room) {
                    return $room->monthly_rent * $room->activeTenants->count();
                });

                $data = array_merge($data, compact(
                    'rooms',
                    'totalRooms',
                    'totalCapacity',
                    'occupiedSlots',
                    'availableSlots',
                    'occupancyRate',
                    'potentialMonthlyRevenue',
                    'currentMonthlyRevenue'
                ));
                break;

            case 'tenants':
                $tenants = Tenant::with(['user', 'room'])
                    ->where('status', 'active')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $data['tenants'] = $tenants;
                break;

            case 'maintenance':
                $requests = MaintenanceRequest::with(['tenant.user', 'room'])
                    ->when($month && $year, function($q) use ($month, $year) {
                        $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();

                $totalRequests = $requests->count();
                $pendingRequests = $requests->where('status', 'pending')->count();
                $inProgressRequests = $requests->where('status', 'in_progress')->count();
                $resolvedRequests = $requests->where('status', 'resolved')->count();
                $rejectedRequests = $requests->where('status', 'rejected')->count();

                $data = array_merge($data, compact(
                    'requests',
                    'totalRequests',
                    'pendingRequests',
                    'inProgressRequests',
                    'resolvedRequests',
                    'rejectedRequests'
                ));
                break;
        }

        return view('admin.reports.index', $data);
    }
}
