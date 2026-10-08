@extends('layouts.admin')

@section('title', 'Room ' . $room->room_number . ' Details')
@section('page_title', 'Room Details')
@section('page_subtitle', 'View and manage Room ' . $room->room_number)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <a href="{{ route('admin.rooms.index') }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Room List
    </a>

    <div class="d-flex gap-2">
        <form action="{{ route('admin.rooms.toggle-availability', $room->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="{{ $room->manual_available ? 'btn-outline-danger-custom' : 'btn-outline-success-custom' }} py-1 px-3" style="font-size: 0.85rem;">
                @if($room->manual_available)
                    <i class="bi bi-slash-circle"></i> Mark as Not Available (Close)
                @else
                    <i class="bi bi-check-lg"></i> Mark as Available (Reopen)
                @endif
            </button>
        </form>

        <a href="{{ route('admin.rooms.edit', $room->id) }}" class="btn-primary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
            <i class="bi bi-pencil"></i> Edit Room
        </a>
    </div>
</div>

<!-- Room Header Banner Card -->
<div class="custom-card mb-4">
    <div class="row align-items-center g-3">
        <div class="col-auto">
            <div class="d-flex align-items-center justify-content-center rounded-3" 
                 style="width: 80px; height: 80px; background-color: var(--table-row-hover); border: 1px solid var(--border-color);">
                <i class="bi bi-door-closed fs-1 text-secondary"></i>
            </div>
        </div>
        <div class="col">
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h3 class="fw-bold mb-0" style="color: var(--text-primary);">Room {{ $room->room_number }}</h3>
                @if($room->room_name)
                    <span class="text-secondary fw-semibold">({{ $room->room_name }})</span>
                @endif

                @if($room->is_available)
                    <span class="badge-pill badge-success">
                        <i class="bi bi-check-lg"></i> Available
                    </span>
                @else
                    <span class="badge-pill badge-danger">
                        <i class="bi bi-x-lg"></i> {{ $room->status_reason }}
                    </span>
                @endif
            </div>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                {{ $room->description ?: 'No room description provided.' }}
            </p>
        </div>
    </div>

    <hr style="border-color: var(--border-color);">

    <div class="row g-3 text-center text-sm-start" style="font-size: 0.88rem;">
        <div class="col-6 col-sm-3">
            <div class="text-secondary" style="font-size: 0.78rem;">ROOM TYPE</div>
            <div class="fw-bold fs-6">{{ $room->room_type }}</div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="text-secondary" style="font-size: 0.78rem;">CAPACITY / SLOTS</div>
            <div class="fw-bold fs-6">
                {{ $room->current_occupancy }} / {{ $room->capacity }}
                <span class="text-secondary fw-normal" style="font-size: 0.8rem;">({{ $room->available_slots }} available)</span>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="text-secondary" style="font-size: 0.78rem;">RENTAL FEE</div>
            <div class="fw-bold fs-6 text-success">₱{{ number_format($room->monthly_rent, 2) }} <span class="fw-normal text-secondary" style="font-size: 0.75rem;">/ mo</span></div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="text-secondary" style="font-size: 0.78rem;">FLOOR LOCATION</div>
            <div class="fw-bold fs-6">{{ $room->floor ?: 'Ground Floor' }}</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Current Occupants Card (Section 32) -->
    <div class="col-lg-6">
        <div class="custom-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0" style="color: var(--text-primary); font-size: 1.05rem;">
                    <i class="bi bi-people-fill me-1 text-primary"></i> Current Occupants
                </h5>
                <span class="badge bg-secondary">{{ $room->activeTenants->count() }} active</span>
            </div>

            @if($room->activeTenants->isEmpty())
                <div class="p-4 text-center text-muted" style="background: var(--table-header-bg); border-radius: 8px;">
                    <i class="bi bi-person-x fs-2 d-block mb-1"></i>
                    No active tenants currently assigned to this room.
                </div>
            @else
                <div class="list-group list-group-flush border-0">
                    @foreach($room->activeTenants as $tenant)
                        <div class="list-group-item d-flex align-items-center justify-content-between p-3 mb-2 rounded border" 
                             style="background: var(--bg-card); border-color: var(--border-color) !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="navbar-user-avatar" style="width: 42px; height: 42px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="fw-bold text-decoration-none" style="color: var(--text-primary); font-size: 0.95rem;">
                                        {{ $tenant->full_name }}
                                    </a>
                                    <div class="text-secondary" style="font-size: 0.78rem;">
                                        <i class="bi bi-telephone me-1"></i>{{ $tenant->contact_number }} • 
                                        Move-in: {{ $tenant->move_in_date ? $tenant->move_in_date->format('M d, Y') : 'Pending' }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span class="badge-pill badge-success" style="font-size: 0.72rem;">Active</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Amenities Card -->
    <div class="col-lg-6">
        <div class="custom-card h-100">
            <h5 class="fw-bold mb-3" style="color: var(--text-primary); font-size: 1.05rem;">
                <i class="bi bi-check2 me-1 text-success"></i> Amenities & Inclusions
            </h5>

            @php $amenities = (array) ($room->amenities ?? []); @endphp
            @if(empty($amenities))
                <div class="p-4 text-center text-muted" style="background: var(--table-header-bg); border-radius: 8px;">
                    No amenities specified for this room.
                </div>
            @else
                <div class="row g-2">
                    @foreach($amenities as $item)
                        <div class="col-sm-6">
                            <div class="p-2 rounded d-flex align-items-center gap-2" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.88rem;">
                                <i class="bi bi-check2 text-success"></i>
                                <span>{{ $item }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Room Photos Gallery Card (Section 20) -->
<div class="custom-card">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1" style="color: var(--text-primary); font-size: 1.05rem;">
                <i class="bi bi-images me-1 text-info"></i> Room Photos Gallery
            </h5>
            <p class="text-secondary mb-0" style="font-size: 0.82rem;">Primary photo is displayed on cards. Other photos appear in the room gallery.</p>
        </div>

        <!-- Quick Upload Additional Photo Form -->
        <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
            @csrf
            @method('PUT')
            <input type="hidden" name="room_number" value="{{ $room->room_number }}">
            <input type="hidden" name="room_type" value="{{ $room->room_type }}">
            <input type="hidden" name="capacity" value="{{ $room->capacity }}">
            <input type="hidden" name="monthly_rent" value="{{ $room->monthly_rent }}">
            @if($room->manual_available)
                <input type="hidden" name="manual_available" value="1">
            @endif

            <input type="file" name="images[]" id="quickImages" multiple class="d-none" accept="image/*" onchange="this.form.submit()">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('quickImages').click()" style="border-radius: 8px;">
                <i class="bi bi-upload"></i> Upload Photos
            </button>
        </form>
    </div>

    @if($room->images->isEmpty())
        <div class="empty-state p-4">
            <div class="empty-state-icon" style="width: 56px; height: 56px;">
                <i class="bi bi-image fs-2"></i>
            </div>
            <h5 class="empty-state-title" style="font-size: 1rem;">No photos uploaded yet</h5>
            <p class="empty-state-desc mb-3" style="font-size: 0.85rem;">Upload clear images to showcase this room to prospective tenants.</p>
            <button type="button" class="btn btn-sm btn-primary-custom" onclick="document.getElementById('quickImages').click()">
                <i class="bi bi-upload"></i> Upload First Photo
            </button>
        </div>
    @else
        <div class="row g-3">
            @foreach($room->images as $image)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="position-relative rounded overflow-hidden shadow-sm" style="border: 2px solid {{ $image->is_primary ? '#38bdf8' : 'var(--border-color)' }};">
                        <img src="{{ $image->url }}" alt="Room photo" style="width: 100%; height: 160px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/room-placeholder.svg') }}';">
                        
                        @if($image->is_primary)
                            <span class="position-absolute top-0 start-0 m-2 badge bg-primary" style="font-size: 0.7rem;">
                                <i class="bi bi-star-fill"></i> Primary
                            </span>
                        @endif

                        <div class="p-2 d-flex justify-content-between align-items-center" style="background-color: var(--bg-card);">
                            @if(!$image->is_primary)
                                <form action="{{ route('admin.rooms.set-primary', [$room->id, $image->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none text-secondary" style="font-size: 0.75rem;">
                                        <i class="bi bi-star"></i> Set Primary
                                    </button>
                                </form>
                            @else
                                <span class="text-primary fw-semibold" style="font-size: 0.75rem;">Default Card Photo</span>
                            @endif

                            <form action="{{ route('admin.rooms.delete-image', [$room->id, $image->id]) }}" method="POST" onsubmit="return confirm('Delete this photo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Delete Photo">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
