<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_code',
        'tenant_id',
        'room_id',
        'billing_month',
        'billing_year',
        'amount',
        'payment_method',
        'payment_date',
        'payment_time',
        'gcash_reference',
        'receipt_path',
        'status',
        'is_edited',
        'notes',
        'remarks',
        'rejection_reason',
        'received_by',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'billing_month' => 'integer',
        'billing_year' => 'integer',
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'verified_at' => 'datetime',
        'is_edited' => 'boolean',
    ];

    public function getFormattedPaymentTimeAttribute(): string
    {
        if ($this->payment_time) {
            return Carbon::parse($this->payment_time)->format('g:i A');
        }
        if ($this->created_at) {
            return $this->created_at->format('g:i A');
        }
        return '';
    }

    public function getPaymentDateTimeFormattedAttribute(): string
    {
        $dateStr = $this->payment_date ? $this->payment_date->format('M d, Y') : ($this->created_at ? $this->created_at->format('M d, Y') : '-');
        $timeStr = $this->formatted_payment_time;
        return $timeStr ? "{$dateStr} • {$timeStr}" : $dateStr;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getReceiptUrlAttribute(): ?string
    {
        if ($this->receipt_path) {
            return asset('storage/' . $this->receipt_path);
        }
        return null;
    }

    public function getBillingPeriodLabelAttribute(): string
    {
        if ($this->billing_month && $this->billing_year) {
            return Carbon::createFromDate($this->billing_year, $this->billing_month, 1)->format('F Y');
        }
        return '-';
    }

    public function getBillingPeriodAttribute(): string
    {
        return $this->getBillingPeriodLabelAttribute();
    }

    public function getMonthNameAttribute(): string
    {
        if ($this->billing_month) {
            return Carbon::createFromDate(2000, $this->billing_month, 1)->format('F');
        }
        return '';
    }

    public function editHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaymentEditHistory::class)->orderBy('id', 'desc');
    }

    public function getEarliestEditHistoryAttribute(): ?PaymentEditHistory
    {
        return $this->editHistories()->oldest('id')->first();
    }

    public function getOriginalValuesSnapshotAttribute(): array
    {
        $earliest = $this->earliest_edit_history;
        if ($earliest && !empty($earliest->old_values)) {
            return $earliest->old_values;
        }

        return [
            'amount' => $this->amount,
            'billing_month' => $this->billing_month,
            'billing_year' => $this->billing_year,
            'payment_method' => $this->payment_method,
            'payment_date' => $this->payment_date ? $this->payment_date->format('Y-m-d') : null,
            'status' => $this->status,
            'gcash_reference' => $this->gcash_reference,
            'remarks' => $this->remarks,
        ];
    }
}
