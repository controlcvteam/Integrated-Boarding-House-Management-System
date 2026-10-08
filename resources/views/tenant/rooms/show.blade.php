@extends('layouts.tenant')

@section('title', 'Room ' . $room->room_number . ' Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('tenant.rooms.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Rooms
    </a>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Room {{ $room->room_number }}</h1>
            <p class="text-muted mb-0">{{ $room->room_type }} &bull; Floor {{ $room->floor }}</p>
        </div>
        <div>
            @if($isMyRoom)
                <span class="badge bg-success fs-6 px-3 py-2 shadow-xs">
                    <i class="bi bi-door-open-fill me-1"></i> Your Current Assigned Room
                </span>
            @elseif($activeRequest && $activeRequest->room_id === $room->id)
                <span class="badge badge-warning fs-6 px-3 py-2" style="font-weight: 600;">
                    <i class="bi bi-clock-history me-1"></i> Request Pending Admin Review
                </span>
            @elseif($room->is_available)
                <button type="button" class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#requestRoomModal">
                    <i class="bi bi-send me-1"></i> Request This Room
                </button>
            @else
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-toggle="modal" data-bs-target="#requestRoomModal">
                    <i class="bi bi-envelope me-1"></i> Request Waitlist / Inquire
                </button>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Gallery & Details -->
    <div class="col-lg-8">
        <!-- Photo Gallery -->
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="position-relative" style="height: 380px; background-color: #f1f5f9;">
                @if($room->primaryImage)
                    <img id="main-preview-img" src="{{ asset('storage/' . $room->primaryImage->image_path) }}" 
                         alt="Room {{ $room->room_number }}" class="w-100 h-100 object-fit-cover"
                         onerror="this.onerror=null; this.src='{{ asset('images/room-placeholder.svg') }}';">
                @else
                    <img id="main-preview-img" src="{{ asset('images/room-placeholder.svg') }}" 
                         alt="Room {{ $room->room_number }}" class="w-100 h-100 object-fit-cover">
                @endif
                <div class="position-absolute top-0 end-0 m-3">
                    @if($isMyRoom)
                        <span class="badge bg-success fs-6 shadow-sm d-inline-flex align-items-center">
                            <i class="bi bi-door-open-fill me-1"></i> Your Current Room
                        </span>
                    @elseif($room->is_available)
                        <span class="badge bg-success fs-6 shadow-sm d-inline-flex align-items-center">
                            <i class="bi bi-check-lg me-1"></i> Available
                        </span>
                    @else
                        <span class="badge bg-danger fs-6 shadow-sm d-inline-flex align-items-center">
                            <i class="bi bi-x-lg me-1"></i> Currently Full / Unavailable
                        </span>
                    @endif
                </div>
            </div>

            @if($room->images->count() > 1)
            <div class="card-body border-top p-3">
                <span class="small text-muted d-block mb-2 fw-semibold">Photo Gallery (Click to preview):</span>
                <div class="d-flex gap-2 overflow-auto py-1">
                    @foreach($room->images as $img)
                        <img src="{{ asset('storage/' . $img->image_path) }}" 
                             alt="Room Thumbnail" 
                             class="rounded border thumbnail-preview" 
                             style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;"
                             onerror="this.onerror=null; this.src='{{ asset('images/room-placeholder.svg') }}';"
                             onclick="document.getElementById('main-preview-img').src=this.src">
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Room Description -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">Room Description</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0" style="white-space: pre-line;">{{ $room->description ?: 'No detailed description provided for this room.' }}</p>
            </div>
        </div>

        <!-- Included Amenities -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">Included Amenities & Features</h5>
            </div>
            <div class="card-body">
                @if($room->amenities && is_array($room->amenities) && count($room->amenities) > 0)
                    <div class="row g-3">
                        @foreach($room->amenities as $amenity)
                        <div class="col-sm-6 col-md-4">
                            <div class="d-flex align-items-center p-2 rounded bg-light border">
                                <i class="bi bi-check2 text-primary fs-5 me-2"></i>
                                <span class="fw-medium" style="color: var(--text-primary);">{{ $amenity }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">Standard room amenities include bed frame, study desk, power outlets, and access to shared facilities.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Specs & Rent Summary -->
    <div class="col-lg-4">
        <!-- Monthly Rent Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Monthly Rent</span>
                <h2 class="fw-bold {{ ($room->is_available && !$isMyRoom) ? 'text-success' : '' }}" style="{{ (!$room->is_available || $isMyRoom) ? 'color: var(--text-primary);' : '' }} mt-1 mb-2">₱{{ number_format($room->monthly_rent, 2) }}</h2>
                <div class="text-muted small mb-4">Due on your monthly move-in anniversary date.</div>

                <div class="list-group list-group-flush small mb-3">
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Room Type</span>
                        <strong style="color: var(--text-primary);">{{ $room->room_type }}</strong>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Floor Level</span>
                        <strong style="color: var(--text-primary);">Floor {{ $room->floor }}</strong>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Capacity</span>
                        <strong style="color: var(--text-primary);">Max {{ $room->capacity }} Person(s)</strong>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Availability</span>
                        @if($isMyRoom)
                            <span class="text-primary fw-bold"><i class="bi bi-door-open me-1"></i> Your Current Assigned Room</span>
                        @elseif($room->is_available)
                            <span class="text-success fw-bold d-inline-flex align-items-center"><i class="bi bi-check-circle me-1"></i> Open for boarding</span>
                        @else
                            <span class="text-danger fw-bold"><i class="bi bi-x-lg me-1"></i> Not Available</span>
                        @endif
                    </div>
                </div>

                @if(!$isMyRoom)
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#requestRoomModal">
                    <i class="bi bi-send me-1"></i> Submit Room Request
                </button>
                @endif
            </div>
        </div>

        <!-- Boarding House Policy Reminder -->
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="bi bi-shield-check text-primary me-2"></i> Reservation Rules</h6>
                <p class="small text-muted mb-0">
                    Room assignments and transfers are subject to Admin approval and confirmation of your move-in date. Once submitted, the Admin will review your request.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Request Room Modal -->
<div class="modal fade" id="requestRoomModal" tabindex="-1" aria-labelledby="requestRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('tenant.rooms.request', $room->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="requestRoomModalLabel">
                        Request Room {{ $room->room_number }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">
                        Submit a request to the Admin to transfer to or reserve <strong>Room {{ $room->room_number }} ({{ $room->room_type }})</strong>.
                    </p>

                    <div class="mb-3">
                        <label for="preferred_move_in_date" class="form-label fw-semibold">Preferred Move-In / Transfer Date</label>
                        <input type="date" name="preferred_move_in_date" id="preferred_move_in_date" class="form-control" min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Notes / Reason for Request</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Optional notes for the Admin (e.g. transfer request, preferred roommate, inquiries)..."></textarea>
                    </div>

                    <div class="alert alert-info py-2 small mb-0">
                        Monthly Rent: <strong>₱{{ number_format($room->monthly_rent, 2) }}</strong>. Payment is handled via Cash or GCash.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
