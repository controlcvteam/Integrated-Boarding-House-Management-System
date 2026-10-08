<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'tenant_code',
        'full_name',
        'profile_picture',
        'contact_number',
        'address',
        'gender',
        'date_of_birth',
        'nationality',
        'emergency_contact',
        'emergency_contact_name',
        'emergency_contact_number',
        'move_in_date',
        'move_out_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'move_in_date' => 'date',
        'move_out_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('payment_date');
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class)->latest();
    }

    public function roomRequests(): HasMany
    {
        return $this->hasMany(RoomRequest::class)->latest();
    }

    public function activeRoomRequest(): HasOne
    {
        return $this->hasOne(RoomRequest::class)->where('status', 'pending');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Compute rent due date for a specific billing month and year based on move_in_date day.
     * Special handling for 29, 30, 31 on shorter months (e.g. February).
     */
    public function getDueDateForMonthYear(int $month, int $year): ?Carbon
    {
        if (!$this->move_in_date) {
            return null;
        }

        $dayOfMoveIn = $this->move_in_date->day;
        $daysInTargetMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $dueDay = min($dayOfMoveIn, $daysInTargetMonth);

        return Carbon::createFromDate($year, $month, $dueDay)->startOfDay();
    }

    /**
     * Get the next upcoming or current rent due date for active tenant.
     */
    public function getNextDueDate(): ?Carbon
    {
        if (!$this->isActive() || !$this->move_in_date) {
            return null;
        }

        $today = Carbon::now();
        $currentMonthDue = $this->getDueDateForMonthYear($today->month, $today->year);

        if ($currentMonthDue && $currentMonthDue->isFuture()) {
            return $currentMonthDue;
        }

        // Check if next month's due date
        $nextMonth = $today->copy()->addMonth();
        return $this->getDueDateForMonthYear($nextMonth->month, $nextMonth->year);
    }

    public function calculateDueDateFor(int $month, int $year): ?Carbon
    {
        return $this->getDueDateForMonthYear($month, $year);
    }

    public function getNextDueDateAttribute(): ?Carbon
    {
        return $this->getNextDueDate() ?? now()->startOfDay();
    }

    /**
     * Get detailed rent status for a specific month and year
     * Returns: ['status' => 'paid'|'partial'|'pending_verification'|'due'|'overdue'|'upcoming'|'unpaid', 'label', 'color', 'paid_amount', 'balance']
     */
    public function getRentStatusForMonthYear(int $month, int $year): array
    {
        $monthlyRent = $this->room ? (float) $this->room->monthly_rent : 0.0;
        $dueDate = $this->getDueDateForMonthYear($month, $year);

        if ($this->relationLoaded('payments')) {
            $monthPayments = $this->payments
                ->where('billing_month', $month)
                ->where('billing_year', $year);
        } else {
            $monthPayments = $this->payments()
                ->where('billing_month', $month)
                ->where('billing_year', $year)
                ->get();
        }

        $paidAmount = (float) $monthPayments->whereIn('status', ['paid', 'verified', 'partial'])->sum('amount');
        $hasPendingVerification = $monthPayments->where('status', 'pending')->isNotEmpty();
        $hasRejected = $monthPayments->where('status', 'rejected')->isNotEmpty();
        $balance = max(0.0, $monthlyRent - $paidAmount);

        $today = Carbon::now()->startOfDay();

        if ($paidAmount >= $monthlyRent && $monthlyRent > 0) {
            return [
                'status' => 'paid',
                'label' => 'Paid',
                'color' => 'success',
                'paid_amount' => $paidAmount,
                'balance' => 0.0,
                'due_date' => $dueDate,
            ];
        }

        if ($hasPendingVerification) {
            return [
                'status' => 'pending_verification',
                'label' => 'Pending Verification',
                'color' => 'warning',
                'paid_amount' => $paidAmount,
                'balance' => $balance,
                'due_date' => $dueDate,
            ];
        }

        if ($paidAmount > 0) {
            $isOverdue = $dueDate && $dueDate->lt($today);
            return [
                'status' => 'partial',
                'label' => $isOverdue ? 'Partially Paid (Overdue)' : 'Partially Paid',
                'color' => $isOverdue ? 'danger' : 'warning',
                'paid_amount' => $paidAmount,
                'balance' => $balance,
                'due_date' => $dueDate,
            ];
        }

        if ($hasRejected && $paidAmount == 0) {
            return [
                'status' => 'rejected',
                'label' => 'Payment Rejected',
                'color' => 'danger',
                'paid_amount' => 0.0,
                'balance' => $monthlyRent,
                'due_date' => $dueDate,
            ];
        }

        if ($dueDate) {
            if ($dueDate->lt($today)) {
                return [
                    'status' => 'overdue',
                    'label' => 'Overdue',
                    'color' => 'danger',
                    'paid_amount' => 0.0,
                    'balance' => $monthlyRent,
                    'due_date' => $dueDate,
                ];
            } elseif ($dueDate->isSameDay($today)) {
                return [
                    'status' => 'due',
                    'label' => 'Due Today',
                    'color' => 'danger',
                    'paid_amount' => 0.0,
                    'balance' => $monthlyRent,
                    'due_date' => $dueDate,
                ];
            } elseif ($dueDate->diffInDays($today) <= 3) {
                return [
                    'status' => 'upcoming',
                    'label' => 'Due in ' . ceil($today->diffInDays($dueDate)) . ' Days',
                    'color' => 'warning',
                    'paid_amount' => 0.0,
                    'balance' => $monthlyRent,
                    'due_date' => $dueDate,
                ];
            } else {
                return [
                    'status' => 'upcoming',
                    'label' => 'Upcoming',
                    'color' => 'info',
                    'paid_amount' => 0.0,
                    'balance' => $monthlyRent,
                    'due_date' => $dueDate,
                ];
            }
        }

        return [
            'status' => 'unpaid',
            'label' => 'Unpaid',
            'color' => 'secondary',
            'paid_amount' => 0.0,
            'balance' => $monthlyRent,
            'due_date' => null,
        ];
    }

    /**
     * Return one of 5 standardized payment categories for a billing month:
     * 'to_pay' (unpaid / rent due / who pay), 'paid', 'pending', 'partial', 'rejected'
     */
    public function getPaymentStatusCategory(?int $month = null, ?int $year = null): string
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $info = $this->getRentStatusForMonthYear($month, $year);
        $status = $info['status'] ?? 'unpaid';

        if (in_array($status, ['paid', 'verified'])) {
            return 'paid';
        }

        if (in_array($status, ['pending', 'pending_verification'])) {
            return 'pending';
        }

        if ($status === 'partial') {
            return 'partial';
        }

        if ($status === 'rejected') {
            return 'rejected';
        }

        if (!$this->room || $this->status === 'moved_out' || $this->status === 'inactive') {
            return 'none';
        }

        return 'to_pay';
    }

    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture && $this->profile_picture !== '0') {
            return asset('storage/' . $this->profile_picture) . '?v=2';
        }

        if ($this->user && $this->user->profile_picture && $this->user->profile_picture !== '0') {
            return asset('storage/' . $this->user->profile_picture) . '?v=2';
        }

        $displayName = $this->full_name ?: ($this->user->name ?? 'Tenant');
        return 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=0284c7&color=ffffff&size=160&bold=true';
    }
}
