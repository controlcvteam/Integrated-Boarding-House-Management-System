@extends('layouts.admin')

@section('title', 'Review Applicant: ' . $tenant->full_name)
@section('page_title', 'Review Registration')
@section('page_subtitle', 'Review applicant details and make approval decision')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.pending-tenants.index') }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Registrations
    </a>
</div>

@php
    $prefRequest = $tenant->activeRoomRequest;
    $prefRoom = $prefRequest ? $prefRequest->room : null;
@endphp

<!-- Applicant Profile Banner -->
<div class="custom-card mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $tenant->profile_picture_url }}" alt="{{ $tenant->full_name }}" class="rounded-circle border shadow-sm" style="width: 64px; height: 64px; object-fit: cover;">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="fw-bold mb-0" style="color: var(--text-primary);">{{ $tenant->full_name }}</h3>
                    @if($tenant->user->account_status === 'pending')
                        <span class="badge-pill badge-warning"><i class="bi bi-hourglass-split"></i> Pending Approval</span>
                    @elseif($tenant->user->account_status === 'approved')
                        <span class="badge-pill badge-success"><i class="bi bi-check-circle-fill"></i> Approved</span>
                    @else
                        <span class="badge-pill badge-danger"><i class="bi bi-x-circle-fill"></i> Rejected</span>
                    @endif
                </div>
                <div class="text-secondary mt-1" style="font-size: 0.88rem;">
                    <span>Code: {{ $tenant->tenant_code ?? 'TEN-PENDING' }}</span> • 
                    <span>Registered: {{ $tenant->created_at->format('F d, Y h:i A') }}</span>
                </div>
            </div>
        </div>

        @if($tenant->user->account_status === 'pending')
            <div class="d-flex gap-2">
                <button type="button" class="btn-outline-danger-custom px-3 py-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle"></i> Reject Application
                </button>
                <button type="button" class="btn-success-custom px-4 py-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="bi bi-check-circle"></i> Approve Account
                </button>
            </div>
        @endif
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Applicant Personal Information Card -->
    <div class="col-lg-6">
        <div class="custom-card h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-2" style="color: var(--text-primary); font-size: 1.05rem; border-color: var(--border-color) !important;">
                <i class="bi bi-card-text me-1 text-primary"></i> Personal Details
            </h5>

            <div class="row g-3" style="font-size: 0.88rem;">
                <div class="col-sm-4 text-secondary">Full Name:</div>
                <div class="col-sm-8 fw-semibold">{{ $tenant->full_name }}</div>

                <div class="col-sm-4 text-secondary">Email Address:</div>
                <div class="col-sm-8 fw-semibold">{{ $tenant->user->email ?? '-' }}</div>

                <div class="col-sm-4 text-secondary">Contact Number:</div>
                <div class="col-sm-8 fw-semibold">{{ $tenant->contact_number }}</div>

                <div class="col-sm-4 text-secondary">Gender:</div>
                <div class="col-sm-8 fw-semibold">{{ $tenant->gender ?: 'Not specified' }}</div>

                <div class="col-sm-4 text-secondary">Date of Birth:</div>
                <div class="col-sm-8 fw-semibold">
                    {{ $tenant->date_of_birth ? $tenant->date_of_birth->format('F d, Y') : 'Not specified' }}
                </div>

                <div class="col-sm-4 text-secondary">Complete Address:</div>
                <div class="col-sm-8 fw-semibold">{{ $tenant->address }}</div>

                <div class="col-sm-4 text-secondary">Nationality:</div>
                <div class="col-sm-8 fw-semibold">{{ $tenant->nationality ?? 'Filipino' }}</div>

                @if($tenant->user->rejection_reason)
                    <div class="col-12 mt-3">
                        <div class="alert alert-danger py-2 px-3 mb-0" style="font-size: 0.82rem;">
                            <strong>Rejection Reason:</strong> {{ $tenant->user->rejection_reason }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Preferred Room Request Card (Section 10) -->
    <div class="col-lg-6">
        <div class="custom-card h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-2" style="color: var(--text-primary); font-size: 1.05rem; border-color: var(--border-color) !important;">
                <i class="bi bi-door-open me-1 text-info"></i> Preferred Room Details
            </h5>

            @if($prefRoom)
                <div class="p-3 rounded mb-3" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 fs-5" style="color: var(--text-primary);">
                            Room {{ $prefRoom->room_number }} 
                            @if($prefRoom->room_name)<span class="text-secondary fw-normal">({{ $prefRoom->room_name }})</span>@endif
                        </h6>
                        @if($prefRoom->is_available)
                            <span class="badge-pill badge-success"><i class="bi bi-check-circle-fill"></i> Available</span>
                        @else
                            <span class="badge-pill badge-danger"><i class="bi bi-x-circle-fill"></i> {{ $prefRoom->status_reason }}</span>
                        @endif
                    </div>

                    <div class="row g-2" style="font-size: 0.85rem;">
                        <div class="col-6 text-secondary">Room Type:</div>
                        <div class="col-6 fw-semibold">{{ $prefRoom->room_type }}</div>

                        <div class="col-6 text-secondary">Monthly Rent:</div>
                        <div class="col-6 fw-bold text-success">₱{{ number_format($prefRoom->monthly_rent, 2) }} / month</div>

                        <div class="col-6 text-secondary">Capacity:</div>
                        <div class="col-6 fw-semibold">{{ $prefRoom->capacity }} Persons</div>

                        <div class="col-6 text-secondary">Current Occupancy:</div>
                        <div class="col-6 fw-semibold">{{ $prefRoom->current_occupancy }} Active Tenants</div>

                        <div class="col-6 text-secondary">Available Slots:</div>
                        <div class="col-6 fw-bold {{ $prefRoom->available_slots > 0 ? 'text-primary' : 'text-danger' }}">
                            {{ $prefRoom->available_slots }} slot(s)
                        </div>

                        <div class="col-6 text-secondary">Floor:</div>
                        <div class="col-6 fw-semibold">{{ $prefRoom->floor ?: 'Ground Floor' }}</div>
                    </div>
                </div>

                <div class="text-secondary" style="font-size: 0.82rem;">
                    <i class="bi bi-info-circle me-1"></i> You can confirm this preferred room during approval, or choose another room from the available list below.
                </div>
            @else
                <div class="p-4 text-center text-muted" style="background: var(--table-header-bg); border-radius: 8px;">
                    <i class="bi bi-bookmark-dash fs-2 d-block mb-1"></i>
                    The applicant did not specify a preferred room during registration.<br>
                    Please select an available room from the list when approving.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- APPROVE ACCOUNT MODAL (Section 11) -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-card p-4 border-0">
            <div class="border-bottom pb-3 mb-3" style="border-color: var(--border-color) !important;">
                <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Approve Tenant Account</h5>
                <p class="text-secondary mb-0" style="font-size: 0.85rem;">Confirm final room assignment and move-in date for {{ $tenant->full_name }}.</p>
            </div>

            <form action="{{ route('admin.pending-tenants.approve', $tenant->id) }}" method="POST">
                @csrf

                <!-- Final Room Assignment Dropdown (Section 28 & 29) -->
                <div class="mb-3">
                    <label for="room_id" class="form-label">Final Room Assignment <span class="text-danger">*</span></label>
                    <select name="room_id" id="room_id" class="form-select" required onchange="updateRoomRentDisplay(this)">
                        <option value="">-- Choose Assigned Room --</option>
                        @foreach($availableRooms as $room)
                            <option value="{{ $room->id }}" 
                                    data-rent="{{ number_format($room->monthly_rent, 2) }}"
                                    {{ ($prefRoom && $prefRoom->id === $room->id) ? 'selected' : '' }}>
                                Room {{ $room->room_number }} ({{ $room->room_type }}) — ₱{{ number_format($room->monthly_rent, 2) }} [{{ $room->available_slots }} slot(s) available]
                                {{ ($prefRoom && $prefRoom->id === $room->id) ? '★ (Applicant Preferred)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text" style="font-size: 0.78rem;">
                        Only rooms with open capacity are displayed. Normal assignment to full rooms is blocked.
                    </div>
                </div>

                <!-- Move-in Date -->
                <div class="mb-3">
                    <label for="move_in_date" class="form-label">Move-In Date <span class="text-danger">*</span></label>
                    <input type="date" name="move_in_date" id="move_in_date" class="form-control" 
                           value="{{ date('Y-m-d') }}" required>
                    <div class="form-text" style="font-size: 0.78rem;">
                        The Move-In Date determines the tenant's monthly rent due date.
                    </div>
                </div>

                <!-- Rent Confirmation Preview -->
                <div class="p-3 rounded mb-3" style="background: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.88rem;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Monthly Rent:</span>
                        <strong class="text-success" id="monthlyRentDisplay">
                            ₱{{ $prefRoom ? number_format($prefRoom->monthly_rent, 2) : '0.00' }}
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Tenant Status:</span>
                        <strong class="text-primary">Active</strong>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-success-custom">
                        <i class="bi bi-check-circle"></i> Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- REJECT ACCOUNT MODAL (Section 12) -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-card p-4 border-0">
            <div class="text-center mb-3">
                <div class="empty-state-icon mx-auto text-danger mb-2" style="width: 56px; height: 56px;">
                    <i class="bi bi-x-circle fs-2"></i>
                </div>
                <h5 class="fw-bold mb-1">Reject Tenant Application</h5>
                <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                    Are you sure you want to reject the application for <strong>{{ $tenant->full_name }}</strong>?
                </p>
            </div>

            <form action="{{ route('admin.pending-tenants.reject', $tenant->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="rejection_reason" class="form-label">Rejection Reason (Optional)</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" class="form-control" 
                              placeholder="e.g. Selected room is no longer available, or registration information could not be verified."></textarea>
                    <div class="form-text" style="font-size: 0.78rem;">
                        This reason will be visible to the applicant on their portal.
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-danger-custom">
                        <i class="bi bi-x-circle"></i> Reject Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateRoomRentDisplay(selectElem) {
        const selected = selectElem.options[selectElem.selectedIndex];
        const rent = selected.getAttribute('data-rent') || '0.00';
        document.getElementById('monthlyRentDisplay').textContent = '₱' + rent;
    }
</script>
@endsection
