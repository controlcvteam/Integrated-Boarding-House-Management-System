<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'account_status',
        'rejection_reason',
        'contact_number',
        'address',
        'gender',
        'profile_picture',
        'date_of_birth',
    ];

    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture && $this->profile_picture !== '0') {
            return asset('storage/' . $this->profile_picture) . '?v=2';
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0284c7&color=ffffff&size=160&bold=true';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AppNotification::class)->latest();
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class)->where('is_read', false)->latest();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    public function isApproved(): bool
    {
        return $this->account_status === 'approved';
    }

    public function isApprovedTenant(): bool
    {
        return $this->role === 'tenant' && $this->account_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->account_status === 'pending';
    }

    public function isPendingTenant(): bool
    {
        return $this->role === 'tenant' && $this->account_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->account_status === 'rejected';
    }

    public function isRejectedTenant(): bool
    {
        return $this->role === 'tenant' && $this->account_status === 'rejected';
    }
}
