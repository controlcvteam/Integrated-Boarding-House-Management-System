@extends('layouts.auth')

@section('title', 'Browse Available Rooms')
@section('card_width', '1240px')

@section('content')
<div class="w-100">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--text-primary);">Available Boarding House Rooms</h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">Explore our clean, secure, and affordable rooms. Register an account to apply and secure your slot!</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('login') }}" class="btn-secondary-custom text-decoration-none">
                <i class="bi bi-box-arrow-in-right"></i> Tenant Login
            </a>
            <a href="{{ route('register') }}" class="btn-primary-custom text-decoration-none">
                <i class="bi bi-person-plus"></i> Apply as Tenant
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="custom-card mb-4 p-3 animate-fade-up">
        <form action="{{ route('rooms.public') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-lg-5 col-md-12">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" 
                           placeholder="Search by room #, type, amenities, wifi, aircon, price, or 'available'..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="available" {{ request('status') == 'available' || request('availability') == 'available' ? 'selected' : '' }}>Available Only</option>
                    <option value="occupied" {{ request('status') == 'occupied' || request('availability') == 'occupied' ? 'selected' : '' }}>Occupied / Full</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach($roomTypes as $tp)
                        <option value="{{ $tp }}" {{ request('type') == $tp ? 'selected' : '' }}>{{ $tp }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <select name="floor" class="form-select">
                    <option value="">All Floors</option>
                    @foreach($floors as $fl)
                        <option value="{{ $fl }}" {{ request('floor') == $fl ? 'selected' : '' }}>Floor {{ $fl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-1 col-md-12 d-flex gap-2">
                <button type="submit" class="btn-primary-custom w-100 justify-content-center" title="Search">
                    <i class="bi bi-search d-none d-lg-inline"></i><span class="d-lg-none">Search</span>
                </button>
                <a href="{{ route('rooms.public') }}" class="btn-secondary-custom" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>

        @if(request('search') || request('floor') || request('type') || request('status') || request('availability'))
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-2 border-top" style="border-color: var(--border-color) !important; font-size: 0.82rem;">
                <span class="text-secondary fw-semibold">Active Search:</span>
                @if(request('search'))
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                        Query: "{{ request('search') }}"
                    </span>
                @endif
                @if(request('status'))
                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                        Status: {{ ucfirst(request('status')) }}
                    </span>
                @endif
                @if(request('type'))
                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                        Type: {{ request('type') }}
                    </span>
                @endif
                @if(request('floor'))
                    <span class="badge bg-secondary-subtle text-secondary border">
                        Floor: {{ request('floor') }}
                    </span>
                @endif
                <a href="{{ route('rooms.public') }}" class="text-danger text-decoration-none ms-2 small fw-semibold">
                    <i class="bi bi-x-circle me-1"></i> Clear Filters
                </a>
                <span class="text-muted ms-auto small">Found {{ $rooms->total() }} matching room(s)</span>
            </div>
        @endif
    </div>

    <!-- Room Cards Grid -->
    <div class="row g-4">
        @forelse($rooms as $room)
        <div class="col-md-6 col-lg-4 animate-fade-up">
            <div class="card border-0 shadow-sm h-100 overflow-hidden room-card transition-hover" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important; border-radius: 12px;">
                <div class="position-relative" style="height: 220px; background-color: var(--table-header-bg);">
                    @if($room->primaryImage)
                        <img src="{{ asset('storage/' . $room->primaryImage->image_path) }}" alt="Room {{ $room->room_number }}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('images/room-placeholder.svg') }}';">
                    @else
                        <img src="{{ asset('images/room-placeholder.svg') }}" alt="Room {{ $room->room_number }}" class="w-100 h-100 object-fit-cover">
                    @endif

                    <div class="position-absolute top-0 end-0 m-3">
                        @if($room->is_available)
                            <span class="badge bg-success shadow-sm d-inline-flex align-items-center">
                                <i class="bi bi-check-lg me-1"></i> Available ({{ $room->available_slots }} slots)
                            </span>
                        @else
                            <span class="badge bg-danger shadow-sm d-inline-flex align-items-center">
                                <i class="bi bi-x-lg me-1"></i> Occupied / Full
                            </span>
                        @endif
                    </div>

                    <div class="position-absolute bottom-0 start-0 m-3">
                        <span class="badge bg-dark bg-opacity-75 text-white">Floor {{ $room->floor }}</span>
                    </div>
                </div>

                <div class="card-body d-flex flex-column p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h4 class="fw-bold mb-0" style="color: var(--text-primary);">Room {{ $room->room_number }}</h4>
                            <span class="text-secondary small">{{ $room->room_type }} {{ $room->room_name ? '• ' . $room->room_name : '' }}</span>
                        </div>
                        <div class="text-end">
                            <span class="fs-5 fw-bold {{ $room->is_available ? 'text-success' : 'text-secondary' }}">₱{{ number_format($room->monthly_rent, 2) }}</span>
                            <div class="text-secondary" style="font-size: 0.75rem;">per month</div>
                        </div>
                    </div>

                    <p class="text-secondary small flex-grow-1 mb-3">
                        {{ Str::limit($room->description ?: 'Clean, secure boarding house room equipped with standard amenities.', 100, '...') }}
                    </p>

                    <div class="py-2 border-top border-bottom my-2 small text-secondary d-flex justify-content-between" style="border-color: var(--border-color) !important;">
                        <span><i class="bi bi-people me-1"></i> Capacity: {{ $room->capacity }} Person(s)</span>
                        <span><i class="bi bi-layers me-1"></i> Floor {{ $room->floor }}</span>
                    </div>

                    @if($room->amenities && is_array($room->amenities))
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @foreach(array_slice($room->amenities, 0, 4) as $amenity)
                                <span class="badge bg-light text-secondary border small">{{ $amenity }}</span>
                            @endforeach
                            @if(count($room->amenities) > 4)
                                <span class="badge bg-light text-muted border small">+{{ count($room->amenities) - 4 }} more</span>
                            @endif
                        </div>
                    @endif

                    <div class="d-grid mt-auto pt-2">
                        @if($room->is_available)
                            <a href="{{ route('register', ['room_id' => $room->id]) }}" class="btn-primary-custom w-100 justify-content-center text-decoration-none">
                                <i class="bi bi-box-arrow-in-right"></i> Register & Apply for Room {{ $room->room_number }}
                            </a>
                        @else
                            <button class="btn btn-secondary w-100" disabled style="font-size: 0.88rem;">
                                Currently Full
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="stat-icon-wrap mx-auto mb-3" style="width: 64px; height: 64px; background-color: var(--table-header-bg); color: var(--text-secondary);">
                <i class="bi bi-door-closed fs-1"></i>
            </div>
            <h5 class="fw-bold mb-1" style="color: var(--text-primary);">No rooms found matching your search</h5>
            <p class="text-secondary small mb-3">Try searching for keywords like "available", room numbers (e.g. 002), floor level, or amenities like "wifi", "aircon", "bathroom".</p>
            <a href="{{ route('rooms.public') }}" class="btn-primary-custom text-decoration-none">
                <i class="bi bi-arrow-counterclockwise"></i> View All Available Rooms
            </a>
        </div>
        @endforelse
    </div>

    @if($rooms->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $rooms->links() }}
    </div>
    @endif
</div>
@endsection
