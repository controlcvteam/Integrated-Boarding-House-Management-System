<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\RoomRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return redirect()->route('auth.pending')
                ->with('error', 'Your tenant record is not yet active.');
        }

        $tenant->load(['room.images', 'room.activeTenants']);
        $room = $tenant->room;

        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Current month payment status
        $currentPayment = Payment::where('tenant_id', $tenant->id)
            ->where('billing_month', $currentMonth)
            ->where('billing_year', $currentYear)
            ->latest()
            ->first();

        // Calculate next rent due date & overdue status
        $currentCycleDueDate = $tenant->calculateDueDateFor($currentMonth, $currentYear);
        $currentRentStatus = $tenant->getRentStatusForMonthYear($currentMonth, $currentYear);
        $isOverdue = in_array($currentRentStatus['status'], ['overdue', 'due']) || 
                     (!$currentPayment && $currentCycleDueDate && now()->startOfDay()->gt($currentCycleDueDate));
        $nextDueDate = $tenant->next_due_date;

        // Recent payments
        $recentPayments = Payment::where('tenant_id', $tenant->id)
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // Recent maintenance requests
        $recentMaintenance = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->latest()
            ->take(5)
            ->get();

        // Pending room requests
        $pendingRoomRequest = RoomRequest::with('room')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('tenant.dashboard', compact(
            'tenant',
            'room',
            'currentPayment',
            'nextDueDate',
            'isOverdue',
            'recentPayments',
            'recentMaintenance',
            'pendingRoomRequest',
            'currentMonth',
            'currentYear'
        ));
    }
}
