@extends('layouts.admin')

@section('title', 'Maintenance Requests')
@section('page_title', 'Maintenance Requests')
@section('page_subtitle', 'Maintenance > Request List')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--text-primary); font-size: 1.25rem;">All Maintenance Requests</h4>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">Review, assign, and update the status of tenant-submitted maintenance requests.</p>
        </div>

        <div>
            <span class="badge bg-secondary-subtle text-secondary border px-3 py-2" style="font-size: 0.8rem; border-radius: 8px;">
                <i class="bi bi-info-circle me-1"></i> Tenant-submitted requests only
            </span>
        </div>
    </div>

    <form action="{{ route('admin.maintenance.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2" style="max-width: 800px;">
        <div class="input-group" style="max-width: 280px;">
            <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                   placeholder="Search requests..." value="{{ request('search') }}">
        </div>

        <select name="status" class="form-select" style="width: auto;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
            <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <select name="priority" class="form-select" style="width: auto;" onchange="this.form.submit()">
            <option value="">All Priorities</option>
            <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
            <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
            <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
        </select>

        @if(request()->anyFilled(['search', 'status', 'priority', 'category']))
            <a href="{{ route('admin.maintenance.index') }}" class="btn btn-sm btn-link text-secondary text-decoration-none">
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
                    <th>Request ID</th>
                    <th>Tenant</th>
                    <th>Room</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Date Submitted</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td class="fw-semibold text-primary">
                            <a href="{{ route('admin.maintenance.show', $req->id) }}" class="text-decoration-none" style="color: inherit;">
                                {{ $req->request_code ?? ('MR-' . $req->id) }}
                            </a>
                        </td>
                        <td class="fw-semibold">
                            @if($req->tenant_id && $req->tenant)
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $req->tenant->profile_picture_url }}" alt="{{ $req->tenant->full_name }}" class="rounded-circle border" style="width: 28px; height: 28px; object-fit: cover;">
                                    <a href="{{ route('admin.tenants.show', $req->tenant_id) }}" class="text-decoration-none" style="color: var(--text-primary);">
                                        {{ $req->tenant->full_name }}
                                    </a>
                                </div>
                            @else
                                <span class="text-muted">{{ $req->tenant->full_name ?? 'Tenant record removed' }}</span>
                            @endif
                        </td>
                        <td>
                            @if($req->room)
                                <a href="{{ route('admin.rooms.show', $req->room->id) }}" class="assigned-room-chip" style="font-size: 0.8rem;" title="View Room Details">
                                    <span class="room-chip-badge">
                                        <i class="bi bi-door-open-fill"></i>
                                        Room {{ $req->room->room_number }}
                                    </span>
                                </a>
                            @else
                                <span class="unassigned-room-chip">-</span>
                            @endif
                        </td>
                        <td style="max-width: 250px;">
                            <div class="fw-semibold text-truncate">{{ $req->title }}</div>
                            <div class="text-secondary text-truncate" style="font-size: 0.76rem;">{{ $req->category }}</div>
                        </td>
                        <td>
                            @php
                                $statusClean = strtolower(str_replace([' ', '_'], '', (string)$req->status));
                            @endphp
                            @if($statusClean === 'inprogress')
                                <span class="badge-pill badge-info"><i class="bi bi-gear-fill"></i> In Progress</span>
                            @elseif($statusClean === 'resolved')
                                <span class="badge-pill badge-success"><i class="bi bi-check-circle-fill"></i> Resolved</span>
                            @elseif($statusClean === 'rejected')
                                <span class="badge-pill badge-danger"><i class="bi bi-x-circle-fill"></i> Rejected</span>
                            @else
                                <span class="badge-pill badge-warning"><i class="bi bi-hourglass-split"></i> Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($req->priority === 'High')
                                <span class="badge-pill badge-danger">{{ $req->priority }}</span>
                            @elseif($req->priority === 'Medium')
                                <span class="badge-pill badge-warning">{{ $req->priority }}</span>
                            @else
                                <span class="badge-pill badge-secondary">{{ $req->priority }}</span>
                            @endif
                        </td>
                        <td style="font-size: 0.82rem;">
                            {{ $req->created_at->format('M d, Y') }}
                            <div class="text-secondary" style="font-size: 0.75rem;">{{ $req->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1 align-items-center table-actions-nowrap">
                                <a href="{{ route('admin.maintenance.show', $req->id) }}" class="action-btn" title="View Request">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.maintenance.edit', $req->id) }}" class="action-btn" title="Edit Request">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="action-btn delete-btn" title="Delete" 
                                        data-bs-toggle="modal" data-bs-target="#deleteMaintModal{{ $req->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="bi bi-tools fs-1"></i>
                                </div>
                                <h4 class="empty-state-title">No maintenance requests found.</h4>
                                <p class="empty-state-desc mb-0">There are currently no maintenance issues reported by tenants.</p>
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

<!-- Delete Maintenance Modals placed outside table container to prevent stacking context clipping -->
@foreach($requests as $req)
    <div class="modal fade text-start" id="deleteMaintModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-card p-4 border-0">
                <div class="text-center mb-3">
                    <div class="empty-state-icon mx-auto text-danger mb-2" style="width: 56px; height: 56px;">
                        <i class="bi bi-trash fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Delete Maintenance Request</h5>
                    <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                        Are you sure you want to delete request <strong>{{ $req->request_code }}</strong> ({{ $req->title }})?
                    </p>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.maintenance.destroy', $req->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-custom">Yes, Delete Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
