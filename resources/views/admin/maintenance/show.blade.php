@extends('layouts.admin')

@section('title', 'Request ' . $maintenance->request_code)
@section('page_title', 'Maintenance Requests')
@section('page_subtitle', 'Maintenance > Request List > Request Detail')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <a href="{{ route('admin.maintenance.index') }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Request List
    </a>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.maintenance.edit', $maintenance->id) }}" class="btn-primary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
            <i class="bi bi-arrow-repeat"></i> Change Status
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Request Information Card -->
    <div class="col-lg-7">
        <div class="custom-card h-100">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3" style="border-color: var(--border-color) !important;">
                <h6 class="fw-bold mb-0" style="font-size: 0.95rem;">
                    Request Information
                </h6>
                <a href="{{ route('admin.maintenance.edit', $maintenance->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.78rem;">
                    Update Status
                </a>
            </div>

            <div class="row g-3" style="font-size: 0.88rem;">
                <div class="col-sm-4 text-secondary">Request ID</div>
                <div class="col-sm-8 fw-semibold">{{ $maintenance->request_code ?? ('MR-' . $maintenance->id) }}</div>

                <div class="col-sm-4 text-secondary">Tenant</div>
                <div class="col-sm-8 fw-bold">
                    @if($maintenance->tenant_id && $maintenance->tenant)
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $maintenance->tenant->profile_picture_url }}" alt="{{ $maintenance->tenant->full_name }}" class="rounded-circle border" style="width: 28px; height: 28px; object-fit: cover;">
                            <a href="{{ route('admin.tenants.show', $maintenance->tenant_id) }}" class="text-decoration-none" style="color: var(--text-primary);">
                                {{ $maintenance->tenant->full_name }}
                            </a>
                        </div>
                    @else
                        <span class="text-muted">{{ $maintenance->tenant->full_name ?? 'Tenant record removed' }}</span>
                    @endif
                </div>

                <div class="col-sm-4 text-secondary">Room</div>
                <div class="col-sm-8 fw-semibold">
                    Room {{ $maintenance->room ? $maintenance->room->room_number . ' (' . $maintenance->room->room_type . ')' : '-' }}
                </div>

                <div class="col-sm-4 text-secondary">Title</div>
                <div class="col-sm-8 fw-bold fs-6" style="color: var(--text-primary);">{{ $maintenance->title }}</div>

                <div class="col-sm-4 text-secondary">Description</div>
                <div class="col-sm-8 p-3 rounded" style="background-color: var(--table-header-bg);">
                    {{ $maintenance->description }}
                </div>

                <div class="col-sm-4 text-secondary">Date Submitted</div>
                <div class="col-sm-8">{{ $maintenance->created_at->format('F d, Y h:i A') }}</div>

                <div class="col-sm-4 text-secondary">Status</div>
                <div class="col-sm-8">
                    @php
                        $statusClean = strtolower(str_replace([' ', '_'], '', (string)$maintenance->status));
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
                </div>

                <div class="col-sm-4 text-secondary">Priority</div>
                <div class="col-sm-8">
                    @if($maintenance->priority === 'High')
                        <span class="badge-pill badge-danger">{{ $maintenance->priority }}</span>
                    @elseif($maintenance->priority === 'Medium')
                        <span class="badge-pill badge-warning">{{ $maintenance->priority }}</span>
                    @else
                        <span class="badge-pill badge-secondary">{{ $maintenance->priority }}</span>
                    @endif
                </div>

                <div class="col-sm-4 text-secondary">Category</div>
                <div class="col-sm-8 fw-semibold">{{ $maintenance->category }}</div>

                <div class="col-sm-4 text-secondary">Assigned To</div>
                <div class="col-sm-8">{{ $maintenance->assigned_to ?: 'Unassigned' }}</div>

                <div class="col-sm-4 text-secondary">Last Updated</div>
                <div class="col-sm-8 text-secondary">{{ $maintenance->updated_at->format('F d, Y h:i A') }}</div>
            </div>
        </div>
    </div>

    <!-- Additional Information & Attachments -->
    <div class="col-lg-5">
        <!-- Card 2: Additional Details -->
        <div class="custom-card mb-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2" style="font-size: 0.95rem; border-color: var(--border-color) !important;">
                Additional Information
            </h6>

            <div class="row g-2" style="font-size: 0.88rem;">
                <div class="col-5 text-secondary">Preferred Date</div>
                <div class="col-7">{{ $maintenance->preferred_date ? $maintenance->preferred_date->format('M d, Y') : 'Not specified' }}</div>

                <div class="col-5 text-secondary">Target Date</div>
                <div class="col-7">{{ $maintenance->target_date ? $maintenance->target_date->format('M d, Y') : 'Not scheduled' }}</div>

                <div class="col-5 text-secondary">Resolved On</div>
                <div class="col-7">{{ $maintenance->resolved_at ? $maintenance->resolved_at->format('M d, Y h:i A') : 'Pending completion' }}</div>

                <div class="col-5 text-secondary">Created By</div>
                <div class="col-7">{{ $maintenance->tenant->full_name }} (Tenant)</div>
            </div>
        </div>

        <!-- Card 3: Attachments with Image Preview -->
        <div class="custom-card mb-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2" style="font-size: 0.95rem; border-color: var(--border-color) !important;">
                Attachments
            </h6>

            @if($maintenance->attachment_path)
                @php
                    $ext = strtolower(pathinfo($maintenance->attachment_path, PATHINFO_EXTENSION));
                    $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                @endphp
                <div class="p-3 rounded" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
                    @if($isImg)
                        <div class="text-center mb-2 p-2 rounded" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                            <a href="{{ $maintenance->attachment_url }}" target="_blank">
                                <img src="{{ $maintenance->attachment_url }}" alt="Attachment Preview" class="img-fluid rounded" style="max-height: 220px; object-fit: contain;">
                            </a>
                        </div>
                    @endif
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <div class="text-truncate fw-semibold" style="font-size: 0.85rem;">
                            <i class="bi {{ $isImg ? 'bi-image' : 'bi-file-earmark' }} me-1 text-primary"></i>
                            {{ basename($maintenance->attachment_path) }}
                        </div>
                        <a href="{{ $maintenance->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.78rem; white-space: nowrap;">
                            <i class="bi bi-box-arrow-up-right me-1"></i> View Full
                        </a>
                    </div>
                </div>
            @else
                <div class="p-3 text-center text-muted" style="background-color: var(--table-header-bg); border-radius: 8px; font-size: 0.85rem;">
                    No attachments uploaded for this request.
                </div>
            @endif
        </div>

        <!-- Card 4: Activity / Remarks History matching wireframe -->
        <div class="custom-card">
            <h6 class="fw-bold mb-3 border-bottom pb-2" style="font-size: 0.95rem; border-color: var(--border-color) !important;">
                Activity & Remarks
            </h6>

            <div class="ps-2 border-start border-2 border-primary" style="font-size: 0.85rem;">
                <div class="mb-3">
                    <span class="text-secondary" style="font-size: 0.75rem;">{{ $maintenance->created_at->format('M d, Y h:i A') }}</span>
                    <div class="fw-semibold">Request submitted by {{ $maintenance->tenant->full_name }}</div>
                </div>

                @if($maintenance->remarks)
                    <div class="p-2 rounded bg-body" style="border: 1px solid var(--border-color);">
                        <strong class="text-primary">Admin Remarks:</strong>
                        <div>{{ $maintenance->remarks }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
