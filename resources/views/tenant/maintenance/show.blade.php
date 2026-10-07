@extends('layouts.tenant')

@section('title', 'Ticket #' . str_pad($request->id, 5, '0', STR_PAD_LEFT) . ' - ' . $request->title)

@section('content')
<div class="mb-4">
    <a href="{{ route('tenant.maintenance.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Maintenance
    </a>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-secondary-subtle text-secondary font-monospace">
                    #MNT-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}
                </span>
                <span class="badge bg-light text-dark border">{{ $request->category }}</span>
            </div>
            <h1 class="h3 fw-bold mb-0">{{ $request->title }}</h1>
        </div>
        <div>
            @php
                $statusKey = strtolower(str_replace([' ', '_'], '', $request->status));
                $remarksToShow = $request->admin_remarks ?: $request->remarks;
            @endphp
            @if($statusKey === 'resolved')
                <span class="badge bg-success fs-6 px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i> Resolved
                </span>
            @elseif($statusKey === 'inprogress')
                <span class="badge bg-info fs-6 px-3 py-2 text-dark">
                    <i class="bi bi-arrow-repeat me-1"></i> In Progress
                </span>
            @elseif($statusKey === 'rejected')
                <span class="badge bg-danger fs-6 px-3 py-2">
                    <i class="bi bi-x-circle me-1"></i> Rejected
                </span>
            @else
                <span class="badge bg-warning fs-6 px-3 py-2 text-dark">
                    <i class="bi bi-clock me-1"></i> Pending Review
                </span>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.maintenance.edit', $request->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit Issue
            </a>
            <form action="{{ route('tenant.maintenance.destroy', $request->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this maintenance ticket #MNT-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Request Details & Attachment -->
    <div class="col-lg-8">
        <!-- Progress Steps -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row text-center position-relative">
                    <div class="col-4">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <h6 class="fw-bold mb-0">Submitted</h6>
                        <small class="text-muted">{{ $request->created_at->format('M d, Y') }}</small>
                    </div>
                    <div class="col-4">
                        <div class="rounded-circle {{ in_array($statusKey, ['inprogress', 'resolved']) ? 'bg-primary text-white' : 'bg-light text-muted border' }} d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-tools"></i>
                        </div>
                        <h6 class="fw-bold mb-0">In Progress</h6>
                        <small class="text-muted">{{ $request->assigned_to ? 'Staff Assigned' : 'Awaiting dispatch' }}</small>
                    </div>
                    <div class="col-4">
                        @if($statusKey === 'rejected')
                            <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-x-lg"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-danger">Rejected</h6>
                            <small class="text-muted">See remarks below</small>
                        @else
                            <div class="rounded-circle {{ $statusKey === 'resolved' ? 'bg-success text-white' : 'bg-light text-muted border' }} d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-check2-all"></i>
                            </div>
                            <h6 class="fw-bold mb-0">Resolved</h6>
                            <small class="text-muted">{{ $request->resolved_at ? $request->resolved_at->format('M d, Y') : 'Pending completion' }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Issue Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">Issue Description</h5>
            </div>
            <div class="card-body">
                <p style="white-space: pre-line;">{{ $request->description }}</p>

                @if($request->attachment_path)
                <div class="mt-4 pt-3 border-top">
                    <span class="fw-semibold small text-muted d-block mb-2">Attached Photo:</span>
                    <a href="{{ asset('storage/' . $request->attachment_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $request->attachment_path) }}" alt="Issue attachment" class="img-thumbnail" style="max-height: 350px;">
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Admin / Resolution Remarks -->
        @if($remarksToShow)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">Admin Remarks & Resolution Notes</h5>
            </div>
            <div class="card-body">
                <div class="p-3 bg-light rounded">
                    <p class="mb-0" style="white-space: pre-line;">{{ $remarksToShow }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column: Specs & Dispatch Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">Ticket Information</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush small">
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Assigned Room</span>
                        <strong style="color: var(--text-primary);">Room {{ $request->room->room_number ?? 'N/A' }}</strong>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Priority</span>
                        @if($request->priority === 'urgent')
                            <span class="badge bg-danger">Urgent</span>
                        @elseif($request->priority === 'high')
                            <span class="badge bg-warning text-dark">High</span>
                        @elseif($request->priority === 'medium')
                            <span class="badge bg-info text-dark">Medium</span>
                        @else
                            <span class="badge bg-secondary">Low</span>
                        @endif
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Assigned Staff</span>
                        <strong style="color: var(--accent-blue);">{{ $request->assigned_to ?: 'Pending Assignment' }}</strong>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Target Resolution</span>
                        <strong style="color: var(--text-primary);">{{ $request->target_date ? $request->target_date->format('M d, Y') : 'To be scheduled' }}</strong>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Date Reported</span>
                        <strong style="color: var(--text-primary);">{{ $request->created_at->format('M d, Y h:i A') }}</strong>
                    </div>
                    @if($request->resolved_at)
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Resolved On</span>
                        <strong class="text-success">{{ $request->resolved_at->format('M d, Y h:i A') }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
