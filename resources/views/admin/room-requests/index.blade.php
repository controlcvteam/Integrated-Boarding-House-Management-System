@extends('layouts.admin')

@section('title', 'Tenant Room Requests')
@section('page_title', 'Room Requests')
@section('page_subtitle', 'Manage room booking requests submitted by tenants')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <form action="{{ route('admin.room-requests.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" style="max-width: 600px;">
        <div class="input-group" style="max-width: 320px;">
            <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                   placeholder="Search tenant or room number..." value="{{ request('search') }}">
        </div>

        <select name="status" class="form-select" style="width: auto;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>

        @if(request()->anyFilled(['search', 'status']))
            <a href="{{ route('admin.room-requests.index') }}" class="btn btn-sm btn-link text-secondary text-decoration-none">
                <i class="bi bi-x-circle"></i> Clear
            </a>
        @endif
    </form>
</div>

<div class="custom-card p-0 overflow-hidden">
    <div class="table-responsive">
        <div class="table-scroll-hint">
            <i class="bi bi-arrows-expand"></i> Swipe table horizontally to see all columns
        </div>
        <table class="custom-table mb-0">
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Requested Room</th>
                    <th>Monthly Rent</th>
                    <th>Current Slots</th>
                    <th>Requested On</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td class="fw-bold" style="color: var(--text-primary);">
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $requesterAvatar = $req->tenant ? $req->tenant->profile_picture_url : ($req->user ? $req->user->profile_picture_url : 'https://ui-avatars.com/api/?name=User');
                                @endphp
                                <img src="{{ $requesterAvatar }}" alt="Requester" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                                <div>
                                    @if($req->tenant_id && $req->tenant)
                                        <a href="{{ route('admin.tenants.show', $req->tenant_id) }}" class="text-decoration-none" style="color: var(--text-primary);">
                                            {{ $req->tenant->full_name }}
                                        </a>
                                        <div class="text-secondary fw-normal" style="font-size: 0.75rem;">
                                            {{ $req->tenant->contact_number }}
                                        </div>
                                    @elseif($req->user)
                                        <span class="text-truncate">{{ $req->user->name }}</span>
                                        <div class="text-secondary fw-normal" style="font-size: 0.75rem;">
                                            {{ $req->user->contact_number ?? $req->user->email }}
                                        </div>
                                    @else
                                        <span class="text-muted">Applicant</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($req->room)
                                <a href="{{ route('admin.rooms.show', $req->room->id) }}" class="assigned-room-chip" style="font-size: 0.8rem;" title="View Room Details">
                                    <span class="room-chip-badge">
                                        <i class="bi bi-door-open-fill"></i>
                                        Room {{ $req->room->room_number }}
                                    </span>
                                    @if($req->room->room_type)
                                        <span class="room-chip-type">{{ $req->room->room_type }}</span>
                                    @endif
                                </a>
                            @else
                                <span class="unassigned-room-chip">-</span>
                            @endif
                        </td>
                        <td class="fw-semibold text-success">
                            ₱{{ number_format($req->room->monthly_rent, 2) }}
                        </td>
                        <td>
                            <span class="{{ $req->room->available_slots > 0 ? 'text-primary' : 'text-danger' }} fw-semibold">
                                {{ $req->room->available_slots }} / {{ $req->room->capacity }} available
                            </span>
                        </td>
                        <td>{{ $req->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            @if($req->status === 'pending')
                                <span class="badge-pill badge-warning"><i class="bi bi-hourglass-split"></i> Pending</span>
                            @elseif($req->status === 'approved')
                                <span class="badge-pill badge-success"><i class="bi bi-check-lg"></i> Approved</span>
                            @elseif($req->status === 'rejected')
                                <span class="badge-pill badge-danger"><i class="bi bi-x-lg"></i> Rejected</span>
                            @else
                                <span class="badge-pill badge-secondary">{{ ucfirst($req->status) }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1 align-items-center table-actions-nowrap">
                                @if($req->status === 'pending')
                                    <button type="button" class="btn-success-custom py-1 px-2 text-nowrap" style="border-radius: 6px; font-size: 0.78rem;" 
                                            data-bs-toggle="modal" data-bs-target="#approveReqModal{{ $req->id }}">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                    <button type="button" class="btn-outline-danger-custom py-1 px-2 text-nowrap" style="border-radius: 6px; font-size: 0.78rem;" 
                                            data-bs-toggle="modal" data-bs-target="#rejectReqModal{{ $req->id }}">
                                        <i class="bi bi-x-lg"></i> Reject
                                    </button>
                                @else
                                    <span class="text-secondary me-1 text-nowrap" style="font-size: 0.78rem;">
                                        {{ $req->admin_remarks ?: 'Completed' }}
                                    </span>
                                @endif
                                <button type="button" class="action-btn delete-btn" title="Delete Room Request"
                                        data-bs-toggle="modal" data-bs-target="#deleteReqModal{{ $req->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="bi bi-bookmark fs-1"></i>
                                </div>
                                <h4 class="empty-state-title">No room requests found.</h4>
                                <p class="empty-state-desc">There are no tenant room requests at this time.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="p-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-color: var(--border-color) !important;">
            <div class="text-secondary" style="font-size: 0.82rem;">
                Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} requests
            </div>
            <div>
                {{ $requests->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Modals placed outside table container to prevent stacking context clipping -->
@foreach($requests as $req)
    @if($req->status === 'pending')
        <!-- Approve Modal -->
        <div class="modal fade text-start" id="approveReqModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content custom-card p-4 border-0">
                    <h5 class="fw-bold mb-2">Approve Room Request</h5>
                    <p class="text-secondary mb-3" style="font-size: 0.88rem;">
                        Assign room to <strong>{{ $req->tenant ? $req->tenant->full_name : ($req->user->name ?? 'Applicant') }}</strong>.
                    </p>

                    <form action="{{ route('admin.room-requests.approve', $req->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Assigned Room</label>
                            <select name="room_id" class="form-select" required>
                                @foreach($availableRooms as $availRoom)
                                    <option value="{{ $availRoom->id }}" {{ $req->room_id == $availRoom->id ? 'selected' : '' }}>
                                        Room {{ $availRoom->room_number }} ({{ $availRoom->room_type }}) — ₱{{ number_format($availRoom->monthly_rent, 2) }}
                                        {{ $req->room_id == $availRoom->id ? '★ (Requested)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Move-In Date</label>
                            <input type="date" name="move_in_date" class="form-control" 
                                   value="{{ ($req->tenant && $req->tenant->move_in_date) ? $req->tenant->move_in_date->format('Y-m-d') : ($req->preferred_move_in_date ? $req->preferred_move_in_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Admin Remarks (Optional)</label>
                            <input type="text" name="admin_remarks" class="form-control" placeholder="Optional notes for tenant">
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-success-custom">
                                <i class="bi bi-check-lg"></i> Approve & Assign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade text-start" id="rejectReqModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content custom-card p-4 border-0">
                    <h5 class="fw-bold mb-2">Reject Room Request</h5>
                    <p class="text-secondary mb-3" style="font-size: 0.88rem;">
                        Reject request for Room {{ $req->room->room_number }} from <strong>{{ $req->tenant ? $req->tenant->full_name : ($req->user->name ?? 'Applicant') }}</strong>.
                    </p>

                    <form action="{{ route('admin.room-requests.reject', $req->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Reason for Rejection</label>
                            <textarea name="admin_remarks" rows="2" class="form-control" placeholder="e.g. Room is undergoing maintenance, or reserved."></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-danger-custom">
                                <i class="bi bi-x-lg"></i> Reject Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Request Modal -->
    <div class="modal fade text-start" id="deleteReqModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-card p-4 border-0">
                <div class="text-center mb-3">
                    <div class="empty-state-icon mx-auto text-danger mb-2" style="width: 56px; height: 56px;">
                        <i class="bi bi-trash fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Delete Room Request</h5>
                    <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                        Are you sure you want to delete this room request? This action cannot be undone.
                    </p>
                </div>

                <div class="p-3 rounded mb-3" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.85rem;">
                    <div class="fw-bold fs-6 mb-1">{{ $req->tenant ? $req->tenant->full_name : ($req->user->name ?? 'Applicant') }}</div>
                    <div class="text-secondary">Requested Room: Room {{ $req->room ? $req->room->room_number : 'N/A' }} ({{ $req->room ? $req->room->room_type : '' }})</div>
                    <div class="text-secondary">Status: {{ ucfirst($req->status) }}</div>
                    <div class="text-secondary">Submitted: {{ $req->created_at->format('M d, Y h:i A') }}</div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.room-requests.destroy', $req->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-custom">
                            <i class="bi bi-trash"></i> Yes, Delete Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
