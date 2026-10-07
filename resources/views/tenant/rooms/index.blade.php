@extends('layouts.tenant')

@section('title', 'Browse Available Rooms')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h1 class="h3 fw-bold mb-1">Boarding House Rooms</h1>
        <p class="text-muted mb-0">Explore room types, floors, amenities, and submit room transfer or reservation requests.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="{{ route('tenant.my-room.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-door-closed me-1"></i> My Current Room
        </a>
    </div>
</div>

@if($activeRequest)
<div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
    <i class="bi bi-hourglass-split fs-4 me-3 text-warning"></i>
    <div>
        <strong>Pending Room Request:</strong> You currently have an active request pending review for <strong>Room {{ $activeRequest->room->room_number ?? 'N/A' }}</strong>. You will be notified once the Admin processes it.
    </div>
</div>
@endif

<!-- Search & Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('tenant.rooms.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-lg-4 col-md-12">
                <label for="search" class="form-label small fw-semibold">Search Rooms</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" id="search" class="form-control border-start-0" placeholder="Search by room #, type, wifi, aircon, 'available'..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6">
                <label for="availability" class="form-label small fw-semibold">Availability</label>
                <select name="availability" id="availability" class="form-select">
                    <option value="">All Rooms</option>
                    <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available Only</option>
                    <option value="occupied" {{ request('availability') == 'occupied' ? 'selected' : '' }}>Occupied / Full</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6">
                <label for="type" class="form-label small fw-semibold">Room Type</label>
                <select name="type" id="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach($types as $tp)
                        <option value="{{ $tp }}" {{ request('type') == $tp ? 'selected' : '' }}>{{ $tp }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6">
                <label for="floor" class="form-label small fw-semibold">Floor Level</label>
                <select name="floor" id="floor" class="form-select">
                    <option value="">All Floors</option>
                    @foreach($floors as $fl)
                        <option value="{{ $fl }}" {{ request('floor') == $fl ? 'selected' : '' }}>Floor {{ $fl }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('tenant.rooms.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Rooms Grid -->
<div class="row g-4">
    @forelse($rooms as $room)
    @php
        $myRoomId = auth()->user()?->tenant?->room_id;
        $isMyRoom = ($myRoomId && $myRoomId === $room->id);
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 room-card position-relative overflow-hidden">
            <!-- Room Image -->
            <div class="position-relative" style="height: 200px; background-color: #f1f5f9;">
                @if($room->primaryImage)
                    <img src="{{ asset('storage/' . $room->primaryImage->image_path) }}" 
                         alt="Room {{ $room->room_number }}" 
                         class="w-100 h-100 object-fit-cover">
                @else
                    <img src="{{ asset('images/room-placeholder.svg') }}" 
                         alt="Room {{ $room->room_number }}" 
                         class="w-100 h-100 object-fit-cover">
                @endif

                <!-- Availability Badge -->
                <div class="position-absolute top-0 end-0 m-3">
                    @if($isMyRoom)
                        <span class="badge bg-success shadow-sm d-inline-flex align-items-center">
                            <i class="bi bi-door-open-fill me-1"></i> Your Current Room
                        </span>
                    @elseif($room->is_available)
                        <span class="badge bg-success shadow-sm d-inline-flex align-items-center">
                            <i class="bi bi-check-lg me-1"></i> Available
                        </span>
                    @else
                        <span class="badge bg-danger shadow-sm d-inline-flex align-items-center">
                            <i class="bi bi-x-lg me-1"></i> Not Available
                        </span>
                    @endif
                </div>

                <div class="position-absolute bottom-0 start-0 m-3">
                    <span class="badge bg-dark bg-opacity-75 text-white">Floor {{ $room->floor }}</span>
                </div>
            </div>

            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h4 class="fw-bold mb-0">Room {{ $room->room_number }}</h4>
                        <span class="text-muted small">{{ $room->room_type }}</span>
                    </div>
                    <div class="text-end">
                        <span class="fs-5 fw-bold {{ ($room->is_available && !$isMyRoom) ? 'text-success' : 'text-secondary' }}">₱{{ number_format($room->monthly_rent, 2) }}</span>
                        <div class="text-muted" style="font-size: 0.75rem;">per month</div>
                    </div>
                </div>

                <p class="text-muted small flex-grow-1">
                    {{ Str::limit($room->description, 90, '...') }}
                </p>

                <!-- Room Specs -->
                <div class="py-2 border-top border-bottom my-2 small text-muted d-flex justify-content-between">
                    <span><i class="bi bi-person me-1"></i> Max {{ $room->capacity }} Person(s)</span>
                    <span>
                        @if($isMyRoom)
                            <span class="text-primary fw-semibold"><i class="bi bi-check2 me-1"></i> Assigned to You</span>
                        @elseif($room->is_available)
                            <span class="text-success fw-semibold"><i class="bi bi-door-open me-1"></i> Open Slots ({{ $room->available_slots }})</span>
                        @else
                            <span class="text-secondary"><i class="bi bi-lock me-1"></i> Full / Unavailable</span>
                        @endif
                    </span>
                </div>

                <!-- Amenities tags -->
                <div class="mb-3">
                    @if($room->amenities && is_array($room->amenities))
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(array_slice($room->amenities, 0, 3) as $amenity)
                                <span class="badge bg-light text-secondary border small">{{ $amenity }}</span>
                            @endforeach
                            @if(count($room->amenities) > 3)
                                <span class="badge bg-light text-muted border small">+{{ count($room->amenities) - 3 }} more</span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="d-grid gap-2 mt-auto">
                    @if($isMyRoom)
                        <a href="{{ route('tenant.my-room.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-door-open me-1"></i> View My Room Details
                        </a>
                    @else
                        <a href="{{ route('tenant.rooms.show', $room->id) }}" class="btn {{ $room->is_available ? 'btn-outline-primary' : 'btn-outline-secondary' }}">
                            View Details & Photos
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-door-closed fs-1 text-muted d-block mb-2"></i>
        <h5 class="fw-bold text-muted">No rooms found matching your search</h5>
        <p class="text-muted">Try clearing your filters to see all available boarding rooms.</p>
        <a href="{{ route('tenant.rooms.index') }}" class="btn btn-sm btn-primary">Reset Filters</a>
    </div>
    @endforelse
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $rooms->links() }}
</div>
@endsection
