@extends('layouts.admin')

@section('title', 'Update Maintenance Status: ' . $maintenance->request_code)
@section('page_title', 'Maintenance Requests')
@section('page_subtitle', 'Maintenance > Request List > Request Detail > Update Status')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.maintenance.show', $maintenance->id) }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Request Detail
    </a>
</div>

<!-- Tenant Submitted Request Details (Read-only) -->
<div class="custom-card mb-4">
    <div class="border-bottom pb-3 mb-3" style="border-color: var(--border-color) !important;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Tenant Maintenance Request Details</h5>
                <p class="text-secondary mb-0" style="font-size: 0.85rem;">Submitted by tenant. Maintenance requests can only be initiated by tenants.</p>
            </div>
            <span class="badge bg-secondary-subtle text-secondary border px-3 py-2" style="font-size: 0.8rem; border-radius: 8px;">
                <i class="bi bi-lock me-1"></i> Tenant Request (Read-Only)
            </span>
        </div>
    </div>

    <div class="row g-3" style="font-size: 0.88rem;">
        <div class="col-md-4">
            <span class="text-secondary d-block" style="font-size: 0.78rem;">TENANT</span>
            <strong>{{ $maintenance->tenant->full_name }}</strong>
        </div>
        <div class="col-md-4">
            <span class="text-secondary d-block" style="font-size: 0.78rem;">ROOM</span>
            <strong>Room {{ $maintenance->room ? $maintenance->room->room_number . ' (' . $maintenance->room->room_type . ')' : 'N/A' }}</strong>
        </div>
        <div class="col-md-4">
            <span class="text-secondary d-block" style="font-size: 0.78rem;">DATE SUBMITTED</span>
            <span>{{ $maintenance->created_at->format('M d, Y h:i A') }}</span>
        </div>
        <div class="col-md-4">
            <span class="text-secondary d-block" style="font-size: 0.78rem;">CATEGORY</span>
            <span class="badge bg-light text-dark border">{{ $maintenance->category }}</span>
        </div>
        <div class="col-md-4">
            <span class="text-secondary d-block" style="font-size: 0.78rem;">PRIORITY</span>
            <span class="badge {{ $maintenance->priority === 'High' ? 'bg-danger' : ($maintenance->priority === 'Medium' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                {{ $maintenance->priority }}
            </span>
        </div>
        <div class="col-md-4">
            <span class="text-secondary d-block" style="font-size: 0.78rem;">CURRENT STATUS</span>
            @php
                $statusClean = strtolower(str_replace([' ', '_'], '', (string)$maintenance->status));
            @endphp
            @if($statusClean === 'inprogress')
                <span class="badge bg-info text-dark">In Progress</span>
            @elseif($statusClean === 'resolved')
                <span class="badge bg-success">Resolved</span>
            @elseif($statusClean === 'rejected')
                <span class="badge bg-danger">Rejected</span>
            @else
                <span class="badge bg-warning text-dark">Pending</span>
            @endif
        </div>
        <div class="col-12">
            <span class="text-secondary d-block" style="font-size: 0.78rem;">ISSUE TITLE</span>
            <div class="fw-bold fs-6 mt-1">{{ $maintenance->title }}</div>
        </div>
        <div class="col-12">
            <span class="text-secondary d-block" style="font-size: 0.78rem;">DESCRIPTION</span>
            <div class="p-3 rounded mt-1" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
                {{ $maintenance->description }}
            </div>
        </div>
    </div>
</div>

<!-- Admin Status Management Form -->
<div class="custom-card">
    <div class="border-bottom pb-3 mb-4" style="border-color: var(--border-color) !important;">
        <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Update Request Status</h5>
        <p class="text-secondary mb-0" style="font-size: 0.85rem;">As Admin, update the status and progress remarks for this maintenance request.</p>
    </div>

    <form action="{{ route('admin.maintenance.update', $maintenance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Status (Pending, In Progress, Resolved, Rejected) -->
            <div class="col-md-6">
                <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                @php
                    $curSel = strtolower(str_replace([' ', '_'], '', (string)old('status', $maintenance->status)));
                @endphp
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required style="border-color: var(--primary);">
                    <option value="Pending" {{ in_array($curSel, ['pending', '']) ? 'selected' : '' }}>Pending</option>
                    <option value="In Progress" {{ $curSel === 'inprogress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Resolved" {{ $curSel === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="Rejected" {{ $curSel === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <div class="text-secondary mt-1" style="font-size: 0.78rem;">Change status as progress occurs. Tenant is notified automatically.</div>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Assigned Staff -->
            <div class="col-md-6">
                <label for="assigned_to" class="form-label">Assigned Staff / Contractor</label>
                <input type="text" name="assigned_to" id="assigned_to" 
                       class="form-control" value="{{ old('assigned_to', $maintenance->assigned_to) }}" 
                       placeholder="e.g. Mike Plumber, Maintenance Staff">
                <div class="text-secondary mt-1" style="font-size: 0.78rem;">Optional: name of the technician handling this task.</div>
            </div>

            <!-- Target Completion Date -->
            <div class="col-md-6">
                <label for="target_date" class="form-label">Target Completion Date</label>
                <input type="date" name="target_date" id="target_date" 
                       class="form-control" value="{{ old('target_date', $maintenance->target_date ? $maintenance->target_date->format('Y-m-d') : '') }}">
            </div>

            <!-- Remarks -->
            <div class="col-md-6">
                <label for="remarks" class="form-label">Admin Remarks / Update Notes for Tenant</label>
                <input type="text" name="remarks" id="remarks" 
                       class="form-control" value="{{ old('remarks', $maintenance->remarks) }}" 
                       placeholder="e.g. Technician scheduled for tomorrow 10am">
            </div>

            <!-- Status Guide Box -->
            <div class="col-md-12 mt-2">
                <div class="p-3 rounded d-flex align-items-center gap-2" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.85rem;">
                    <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                    <div>
                        <strong>Status Flow:</strong> 
                        <span class="text-warning fw-semibold">Pending</span> &rarr; 
                        <span class="text-info fw-semibold">In Progress</span> &rarr; 
                        <span class="text-success fw-semibold">Resolved</span> (or <span class="text-danger fw-semibold">Rejected</span>).
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
            <a href="{{ route('admin.maintenance.show', $maintenance->id) }}" class="btn-secondary-custom text-decoration-none">Cancel</a>
            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-check-lg"></i> Update Status
            </button>
        </div>
    </form>
</div>
@endsection
