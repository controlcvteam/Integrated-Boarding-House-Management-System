@extends('layouts.tenant')

@section('title', 'My Assigned Room')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h1 class="h3 fw-bold mb-1">My Assigned Room: Room {{ $room->room_number }}</h1>
        <p class="text-muted mb-0">Complete details of your boarding accommodation and lease specifications.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="{{ route('tenant.rooms.index') }}" class="btn btn-outline-primary me-2">
            <i class="bi bi-arrow-repeat me-1"></i> Request Room Transfer
        </a>
        <a href="{{ route('tenant.maintenance.create') }}" class="btn btn-outline-secondary">
            <i class="bi bi-tools me-1"></i> REPORT ISSUE
        </a>
    </div>
</div>

@if($activeRequest)
<div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
    <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
    <div>
        <strong>Pending Room Transfer Request:</strong> You have submitted a request for <strong>Room {{ $activeRequest->room->room_number ?? 'N/A' }}</strong>. You will be transferred once approved by the Admin.
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Room Photos Carousel/Preview -->
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="position-relative" style="height: 380px; background-color: #f1f5f9;">
                @if($room->primaryImage)
                    <img id="my-room-img" src="{{ asset('storage/' . $room->primaryImage->image_path) }}" 
                         alt="Room {{ $room->room_number }}" class="w-100 h-100 object-fit-cover">
                @else
                    <img id="my-room-img" src="{{ asset('images/room-placeholder.svg') }}" 
                         alt="Room {{ $room->room_number }}" class="w-100 h-100 object-fit-cover">
                @endif
                <div class="position-absolute bottom-0 start-0 m-3">
                    <span class="badge bg-primary fs-6 px-3 py-2 shadow-sm">
                        Floor {{ $room->floor }} &bull; {{ $room->room_type }}
                    </span>
                </div>
            </div>

            @if($room->images->count() > 1)
            <div class="card-body border-top p-3">
                <span class="small text-muted d-block mb-2 fw-semibold">Room Gallery:</span>
                <div class="d-flex gap-2 overflow-auto py-1">
                    @foreach($room->images as $img)
                        <img src="{{ asset('storage/' . $img->image_path) }}" 
                             alt="Room Thumbnail" 
                             class="rounded border thumbnail-preview" 
                             style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;"
                             onclick="document.getElementById('my-room-img').src='{{ asset('storage/' . $img->image_path) }}'">
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Room Amenities -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">Room Amenities</h5>
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
                    <p class="text-muted mb-0">Standard room amenities include bed frame, mattress, study table, and access to shared kitchen and laundry facilities.</p>
                @endif
            </div>
        </div>

        <!-- Room Description -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">Room Notes & Description</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0" style="white-space: pre-line;">{{ $room->description ?: 'No additional notes provided.' }}</p>
            </div>
        </div>
    </div>

    <!-- Right Column: Lease & Rent Overview -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">Lease Agreement</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush small">
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Tenant Code</span>
                        <strong class="tenant-id-highlight">{{ $tenant->tenant_code }}</strong>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Monthly Rent</span>
                        <strong class="text-success fs-6">₱{{ number_format($room->monthly_rent, 2) }}</strong>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Move-In Date</span>
                        <strong style="color: var(--text-primary);">{{ $tenant->move_in_date ? $tenant->move_in_date->format('F d, Y') : 'N/A' }}</strong>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Monthly Due Date</span>
                        <strong class="fw-bold" style="color: var(--accent-blue);">{{ $tenant->next_due_date->format('F d, Y') }}</strong>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Lease Status</span>
                        <span class="badge bg-success-subtle text-success">Active</span>
                    </div>
                </div>

                <div class="mt-4 d-grid gap-2">
                    <a href="{{ route('tenant.payments.submit') }}" class="btn btn-primary">
                        <i class="bi bi-wallet2 me-1"></i> SUBMIT A PAYMENT
                    </a>
                    <a href="{{ route('tenant.payments.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-receipt me-1"></i> View Payment History
                    </a>
                </div>
            </div>
        </div>

        <!-- Emergency Contact on File -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Emergency Contact</h5>
                <a href="{{ route('tenant.settings.index') }}" class="small text-primary text-decoration-none">Update</a>
            </div>
            <div class="card-body small">
                <div class="mb-2">
                    <span class="text-muted d-block">Contact Person:</span>
                    <strong>{{ $tenant->emergency_contact_name ?? 'None registered' }}</strong>
                </div>
                <div>
                    <span class="text-muted d-block">Phone Number:</span>
                    <strong>{{ $tenant->emergency_contact_number ?? 'None registered' }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
