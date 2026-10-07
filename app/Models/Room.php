<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_name',
        'room_type',
        'capacity',
        'monthly_rent',
        'floor',
        'description',
        'amenities',
        'manual_available',
    ];

    protected $casts = [
        'amenities' => 'array',
        'manual_available' => 'boolean',
        'monthly_rent' => 'decimal:2',
        'capacity' => 'integer',
    ];

    protected $appends = [
        'current_occupancy',
        'available_slots',
        'is_available',
        'status_label',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(RoomImage::class)->where('is_primary', true);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function activeTenants(): HasMany
    {
        return $this->hasMany(Tenant::class)->where('status', 'active');
    }

    public function roomRequests(): HasMany
    {
        return $this->hasMany(RoomRequest::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function getCurrentOccupancyAttribute(): int
    {
        if ($this->relationLoaded('activeTenants')) {
            return $this->activeTenants->count();
        }
        return $this->activeTenants()->count();
    }

    public function getAvailableSlotsAttribute(): int
    {
        return max(0, $this->capacity - $this->current_occupancy);
    }

    public function getIsAvailableAttribute(): bool
    {
        if (!$this->manual_available) {
            return false;
        }

        return $this->current_occupancy < $this->capacity;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_available ? 'Available' : 'Not Available';
    }

    public function getStatusReasonAttribute(): string
    {
        if (!$this->manual_available) {
            return 'Maintenance / Closure';
        }
        if ($this->current_occupancy >= $this->capacity) {
            return 'Full / Occupied';
        }
        return 'Available';
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->primaryImage ?? $this->images->first();
        if ($primary && $primary->image_path) {
            return asset('storage/' . $primary->image_path);
        }
        return asset('images/room-placeholder.svg');
    }

    /**
     * Intelligent, synonym-aware search for rooms.
     * Matches room number, room name, type, floor, price, description, amenities,
     * common synonyms (wifi, aircon, bathroom, etc.), and availability status.
     */
    public function scopeSearchRelated($query, ?string $search)
    {
        if (!$search || !trim($search)) {
            return $query;
        }

        $rawSearch = trim($search);

        // Common synonym expansions
        $synonymMap = [
            'aircon' => ['air conditioner', 'aircon', 'air conditioning', 'ac'],
            'ac' => ['air conditioner', 'aircon', 'air conditioning'],
            'wifi' => ['wi-fi', 'wifi', 'internet'],
            'wi-fi' => ['wi-fi', 'wifi', 'internet'],
            'internet' => ['wi-fi', 'wifi', 'internet'],
            'cr' => ['bathroom', 'comfort room', 'restroom', 'toilet', 'private bathroom'],
            'bathroom' => ['bathroom', 'private bathroom', 'cr', 'restroom', 'toilet'],
            'toilet' => ['bathroom', 'private bathroom', 'cr', 'toilet'],
            'desk' => ['study desk', 'desk', 'table'],
            'table' => ['study desk', 'desk', 'table'],
            'study' => ['study desk'],
            'bed' => ['bed frame', 'bed', 'bunk'],
            'cabinet' => ['closet', 'cabinet', 'wardrobe'],
            'closet' => ['closet', 'cabinet', 'wardrobe'],
            'wardrobe' => ['closet', 'cabinet', 'wardrobe'],
            'heater' => ['water heater', 'heater', 'hot shower'],
            'shower' => ['water heater', 'shower', 'bathroom'],
            'hot' => ['water heater'],
            'solo' => ['single room', 'single'],
            'single' => ['single room', 'single'],
            'double' => ['double room', 'double'],
            'shared' => ['bedspace', 'shared', 'double room'],
            'dorm' => ['bedspace', 'dormitory'],
        ];

        $digits = preg_replace('/[^\d]/', '', $rawSearch);
        $unpaddedDigits = ltrim($digits, '0');

        $isSearchingAvailable = (bool) preg_match('/\b(avail|available|vacant|free|open|empty)\b/i', $rawSearch);
        $isSearchingOccupied = (bool) preg_match('/\b(occupied|full|taken|unavailable)\b/i', $rawSearch);

        // Floor extraction
        $floorNum = null;
        if (preg_match('/(?:floor|flr|fl)\s*(\d+)/i', $rawSearch, $m)) {
            $floorNum = $m[1];
        } elseif (preg_match('/(\d+)(?:st|nd|rd|th)?\s*floor/i', $rawSearch, $m)) {
            $floorNum = $m[1];
        }

        $words = array_filter(preg_split('/[\s,\-\/]+/', $rawSearch), fn($w) => strlen($w) >= 2);

        // Collect related synonym expansion words
        $expandedTerms = [];
        foreach ($words as $w) {
            $wLower = strtolower($w);
            if (isset($synonymMap[$wLower])) {
                foreach ($synonymMap[$wLower] as $syn) {
                    $expandedTerms[] = $syn;
                }
            }
        }
        $expandedTerms = array_unique($expandedTerms);

        $castType = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'TEXT' : 'CHAR';

        return $query->where(function ($sub) use ($rawSearch, $digits, $unpaddedDigits, $isSearchingAvailable, $isSearchingOccupied, $floorNum, $words, $expandedTerms, $castType) {
            // Direct matching on basic room fields
            $sub->where('room_number', 'like', "%{$rawSearch}%")
                ->orWhere('room_name', 'like', "%{$rawSearch}%")
                ->orWhere('room_type', 'like', "%{$rawSearch}%")
                ->orWhere('description', 'like', "%{$rawSearch}%")
                ->orWhereRaw("LOWER(CAST(amenities AS {$castType})) LIKE ?", ["%" . strtolower($rawSearch) . "%"])
                ->orWhereRaw("REPLACE(LOWER(CAST(amenities AS {$castType})), '-', '') LIKE ?", ["%" . str_replace('-', '', strtolower($rawSearch)) . "%"])
                ->orWhere('floor', 'like', "%{$rawSearch}%")
                ->orWhere('monthly_rent', 'like', "%{$rawSearch}%");

            if (!empty($digits)) {
                $sub->orWhere('room_number', 'like', "%{$digits}%")
                    ->orWhere('monthly_rent', 'like', "%{$digits}%");
            }
            if (!empty($unpaddedDigits)) {
                $sub->orWhere('room_number', 'like', "%{$unpaddedDigits}%")
                    ->orWhere('floor', 'like', "%{$unpaddedDigits}%");
            }
            if ($floorNum !== null) {
                $sub->orWhere('floor', 'like', "%{$floorNum}%");
            }

            // Keyword "available" / "vacant"
            if ($isSearchingAvailable) {
                $sub->orWhere(function ($availQ) {
                    $availQ->where('manual_available', true)
                           ->whereRaw("(SELECT COUNT(*) FROM tenants WHERE tenants.room_id = rooms.id AND tenants.status = 'active') < rooms.capacity");
                });
            }

            // Keyword "occupied" / "full"
            if ($isSearchingOccupied) {
                $sub->orWhere(function ($occQ) {
                    $occQ->where('manual_available', false)
                         ->orWhereRaw("(SELECT COUNT(*) FROM tenants WHERE tenants.room_id = rooms.id AND tenants.status = 'active') >= rooms.capacity");
                });
            }

            // Expanded synonym terms
            foreach ($expandedTerms as $term) {
                $sub->orWhere('room_name', 'like', "%{$term}%")
                    ->orWhere('room_type', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhereRaw("LOWER(CAST(amenities AS {$castType})) LIKE ?", ["%" . strtolower($term) . "%"])
                    ->orWhereRaw("REPLACE(LOWER(CAST(amenities AS {$castType})), '-', '') LIKE ?", ["%" . str_replace('-', '', strtolower($term)) . "%"]);
            }

            // Individual words matching
            foreach ($words as $word) {
                $wLower = strtolower($word);
                if (in_array($wLower, ['room', 'rooms', 'the', 'for', 'with', 'and', 'near', 'in', 'at'])) {
                    continue;
                }
                $sub->orWhere('room_number', 'like', "%{$word}%")
                    ->orWhere('room_name', 'like', "%{$word}%")
                    ->orWhere('room_type', 'like', "%{$word}%")
                    ->orWhere('description', 'like', "%{$word}%")
                    ->orWhereRaw("LOWER(CAST(amenities AS {$castType})) LIKE ?", ["%" . $wLower . "%"])
                    ->orWhereRaw("REPLACE(LOWER(CAST(amenities AS {$castType})), '-', '') LIKE ?", ["%" . str_replace('-', '', $wLower) . "%"])
                    ->orWhere('floor', 'like', "%{$word}%");
            }
        });
    }
}
