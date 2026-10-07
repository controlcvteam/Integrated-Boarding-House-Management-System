@extends('layouts.admin')

@section('title', 'Pending Tenant Registrations')
@section('page_title', 'Pending Registrations')
@section('page_subtitle', 'Review and process new tenant registration applications')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <form action="{{ route('admin.pending-tenants.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" style="max-width: 600px;">
        <div class="input-group" style="max-width: 320px;">
            <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                   placeholder="Search applicant name, email, phone..." value="{{ request('search') }}">
        </div>

        <select name="status" class="form-select" style="width: auto;" onchange="this.form.submit()">
            <option value="pending" {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        @if(request()->anyFilled(['search']) || request('status') != 'pending')
            <a href="{{ route('admin.pending-tenants.index') }}" class="btn btn-sm btn-link text-secondary text-decoration-none">
                <i class="bi bi-x-circle"></i> Reset
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
                    <th>Applicant Name</th>
                    <th>Email Address</th>
                    <th>Contact Number</th>
                    <th>Registered On</th>
                    <th>Preferred Room</th>
                    <th>Account Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                    <tr>
                        <td class="fw-bold" style="color: var(--text-primary);">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $tenant->profile_picture_url }}" alt="{{ $tenant->full_name }}" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                                <div>
                                    <a href="{{ route('admin.pending-tenants.show', $tenant->id) }}" class="text-decoration-none" style="color: var(--text-primary);">
                                        {{ $tenant->full_name }}
                                    </a>
                                    <div class="text-secondary fw-normal" style="font-size: 0.75rem;">
                                        {{ $tenant->tenant_code ?? 'Applicant' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $tenant->user->email ?? '-' }}</td>
                        <td>{{ $tenant->contact_number }}</td>
                        <td>{{ $tenant->created_at->format('M d, Y') }}</td>
                        <td>
                            @php
                                $prefRequest = $tenant->activeRoomRequest;
                                $prefRoom = $prefRequest ? $prefRequest->room : null;
                            @endphp

                            @if($prefRoom)
                                <span class="badge bg-primary text-white" style="font-size: 0.78rem;">
                                    Room {{ $prefRoom->room_number }}
                                </span>
                                <div class="text-secondary" style="font-size: 0.72rem;">
                                    {{ $prefRoom->room_type }} • ₱{{ number_format($prefRoom->monthly_rent, 2) }}
                                </div>
                            @elseif($tenant->room)
                                <span class="badge bg-success text-white" style="font-size: 0.78rem;">
                                    Assigned: Room {{ $tenant->room->room_number }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size: 0.82rem;">None specified</span>
                            @endif
                        </td>
                        <td>
                            @if($tenant->user->account_status === 'pending')
                                <span class="badge-pill badge-warning">
                                    <i class="bi bi-hourglass-split"></i> Pending
                                </span>
                            @elseif($tenant->user->account_status === 'approved')
                                <span class="badge-pill badge-success">
                                    <i class="bi bi-check-circle-fill"></i> Approved
                                </span>
                            @else
                                <span class="badge-pill badge-danger">
                                    <i class="bi bi-x-circle-fill"></i> Rejected
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.pending-tenants.show', $tenant->id) }}" class="btn-primary-custom text-decoration-none py-1 px-3" style="font-size: 0.82rem;">
                                <i class="bi bi-eye"></i> Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                                <h4 class="empty-state-title">No pending registrations found.</h4>
                                <p class="empty-state-desc">There are no tenant account applications waiting for review at this time.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tenants->hasPages())
        <div class="p-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-color: var(--border-color) !important;">
            <div class="text-secondary" style="font-size: 0.82rem;">
                Showing {{ $tenants->firstItem() }} to {{ $tenants->lastItem() }} of {{ $tenants->total() }} registrations
            </div>
            <div>
                {{ $tenants->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
