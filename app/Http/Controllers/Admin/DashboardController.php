<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. KPI Statistics backing from MySQL
        $pendingTenantsCount = Tenant::whereHas('user', function ($q) {
            $q->where('role', 'tenant')->where('account_status', 'pending');
        })->count();
        $totalTenants = Tenant::whereHas('user', function ($q) {
            $q->where('role', 'tenant');
        })->count();
        $activeTenants = Tenant::where('status', 'active')->count();

        $allRooms = Room::with('activeTenants')->get();
        $totalRooms = $allRooms->count();
        $availableRooms = $allRooms->filter(fn($r) => $r->is_available)->count();
        $occupiedRooms = $allRooms->filter(fn($r) => !$r->is_available)->count();

        // Financial calculations for current month
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Expected Monthly Rent from active tenants assigned to rooms
        $activeTenantsList = Tenant::where('status', 'active')->with(['room', 'user'])->get();
        $expectedMonthlyRent = $activeTenantsList->sum(fn($t) => $t->room ? $t->room->monthly_rent : 0);

        // Payments Received this month
        $paymentsThisMonth = Payment::where('billing_month', $currentMonth)
            ->where('billing_year', $currentYear)
            ->whereIn('status', ['paid', 'verified', 'partial'])
            ->sum('amount');

        $outstandingRent = max(0, $expectedMonthlyRent - $paymentsThisMonth);

        // Overdue tenants calculation
        $overdueCount = 0;
        foreach ($activeTenantsList as $t) {
            $status = $t->getRentStatusForMonthYear($currentMonth, $currentYear);
            if (in_array($status['status'], ['overdue', 'due'])) {
                $overdueCount++;
            }
        }

        // Maintenance and GCash counts
        $pendingGcashCount = Payment::where('payment_method', 'gcash')
            ->where('status', 'pending')
            ->count();
        $pendingMaintenance = MaintenanceRequest::where('status', 'Pending')->count();
        $inProgressMaintenance = MaintenanceRequest::where('status', 'In Progress')->count();

        // 2. Recent activities
        $recentRegistrations = Tenant::where('status', 'pending')
            ->with(['user', 'activeRoomRequest.room'])
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['tenant', 'room'])
            ->latest('payment_date')
            ->take(5)
            ->get();

        $recentMaintenance = MaintenanceRequest::with(['tenant', 'room'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Rent Due Calendar Data (Section 39, 40, 41)
        $calendarMonth = (int) $request->input('cal_month', $currentMonth);
        $calendarYear = (int) $request->input('cal_year', $currentYear);

        $calDate = Carbon::createFromDate($calendarYear, $calendarMonth, 1);
        $daysInMonth = $calDate->daysInMonth;
        $firstDayOfWeek = $calDate->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.

        // Build calendar events map: day => array of tenant rent event objects
        $calendarEvents = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $calendarEvents[$d] = [];
        }

        foreach ($activeTenantsList as $tenant) {
            if (!$tenant->move_in_date || !$tenant->room) {
                continue;
            }

            $dueDate = $tenant->getDueDateForMonthYear($calendarMonth, $calendarYear);
            if (!$dueDate) {
                continue;
            }

            $dueDay = $dueDate->day;
            $rentStatus = $tenant->getRentStatusForMonthYear($calendarMonth, $calendarYear);

            // Fetch payment record if any
            $paymentRecord = Payment::where('tenant_id', $tenant->id)
                ->where('billing_month', $calendarMonth)
                ->where('billing_year', $calendarYear)
                ->latest()
                ->first();

            $calendarEvents[$dueDay][] = [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->full_name,
                'room_number' => $tenant->room->room_number,
                'room_type' => $tenant->room->room_type,
                'monthly_rent' => (float) $tenant->room->monthly_rent,
                'due_date' => $dueDate->format('M d, Y'),
                'due_day' => $dueDay,
                'status' => $rentStatus['status'],
                'label' => $rentStatus['label'],
                'color' => $rentStatus['color'],
                'paid_amount' => $rentStatus['paid_amount'],
                'balance' => $rentStatus['balance'],
                'payment_method' => $paymentRecord ? $paymentRecord->payment_method : null,
                'payment_id' => $paymentRecord ? $paymentRecord->id : null,
                'payment_code' => $paymentRecord ? $paymentRecord->payment_code : null,
                'gcash_ref' => $paymentRecord ? $paymentRecord->gcash_reference : null,
                'receipt_url' => $paymentRecord ? $paymentRecord->receipt_url : null,
            ];
        }

        return view('admin.dashboard', compact(
            'pendingTenantsCount',
            'totalTenants',
            'activeTenants',
            'totalRooms',
            'availableRooms',
            'occupiedRooms',
            'allRooms',
            'expectedMonthlyRent',
            'paymentsThisMonth',
            'outstandingRent',
            'overdueCount',
            'pendingGcashCount',
            'pendingMaintenance',
            'inProgressMaintenance',
            'recentRegistrations',
            'recentPayments',
            'recentMaintenance',
            'calendarMonth',
            'calendarYear',
            'daysInMonth',
            'firstDayOfWeek',
            'calendarEvents',
            'calDate'
        ));
    }
}
