@extends('layouts.tenant')

@section('title', 'Maintenance Requests')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h1 class="h3 fw-bold mb-1">Maintenance & Repairs</h1>
        <p class="text-muted mb-0">Report facilities issues, track repair progress, and view Admin updates.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('tenant.maintenance.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-tools me-1"></i> REPORT ISSUE
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3">
        <h5 class="fw-bold mb-0">My Reported Issues</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint">
                <i class="bi bi-arrows-expand"></i> Swipe table horizontally to see all columns
            </div>
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ticket #</th>
                        <th>Category</th>
                        <th>Issue Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned Staff</th>
                        <th>Date Reported</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="fw-semibold text-primary">#MNT-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $req->category }}</span>
                        </td>
                        <td class="fw-semibold">{{ $req->title }}</td>
                        <td>
                            @if($req->priority === 'urgent')
                                <span class="badge bg-danger">Urgent</span>
                            @elseif($req->priority === 'high')
                                <span class="badge bg-warning text-dark">High</span>
                            @elseif($req->priority === 'medium')
                                <span class="badge bg-info text-dark">Medium</span>
                            @else
                                <span class="badge bg-secondary">Low</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusKey = strtolower(str_replace([' ', '_'], '', $req->status));
                            @endphp
                            @if($statusKey === 'resolved')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-check-circle me-1"></i> Resolved
                                </span>
                            @elseif($statusKey === 'inprogress')
                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                    <i class="bi bi-arrow-repeat me-1"></i> In Progress
                                </span>
                            @elseif($statusKey === 'rejected')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-x-circle me-1"></i> Rejected
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                    <i class="bi bi-clock me-1"></i> Pending Review
                                </span>
                            @endif
                        </td>
                        <td>{{ $req->assigned_to ?: 'Unassigned' }}</td>
                        <td>{{ $req->created_at->format('M d, Y') }}</td>
                        <td class="text-end text-nowrap">
                            <div class="d-inline-flex gap-1 align-items-center">
                                <a href="{{ route('tenant.maintenance.show', $req->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('tenant.maintenance.edit', $req->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Edit Ticket">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('tenant.maintenance.destroy', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this maintenance ticket #MNT-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete Ticket">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-check2-circle fs-1 text-success d-block mb-2"></i>
                            No maintenance issues filed. Everything is in order!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($requests->hasPages())
    <div class="card-footer bg-transparent py-3">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection
