<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentEditHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'user_id',
        'editor_name',
        'reason',
        'changed_fields',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get displayable editor name (falls back to saved editor_name or user relationship)
     */
    public function getEditorDisplayNameAttribute(): string
    {
        if ($this->user && $this->user->name) {
            return $this->user->name;
        }
        return $this->editor_name ?: 'Landlord';
    }
}
