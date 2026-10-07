<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code',
        'tenant_id',
        'room_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'preferred_date',
        'attachment_path',
        'assigned_to',
        'target_date',
        'remarks',
        'admin_remarks',
        'resolved_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'target_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if ($this->attachment_path) {
            return asset('storage/' . $this->attachment_path);
        }
        return null;
    }

    public function getStatusLabelAttribute(): string
    {
        $clean = strtolower(str_replace([' ', '_'], '', (string)$this->status));
        return match ($clean) {
            'inprogress' => 'In Progress',
            'resolved' => 'Resolved',
            'rejected' => 'Rejected',
            default => 'Pending',
        };
    }

    public function getStatusColorAttribute(): string
    {
        $clean = strtolower(str_replace([' ', '_'], '', (string)$this->status));
        return match ($clean) {
            'inprogress' => 'info',
            'resolved' => 'success',
            'rejected' => 'danger',
            default => 'warning',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match (strtolower($this->priority)) {
            'urgent' => 'danger',
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'info',
            default => 'secondary',
        };
    }
}
