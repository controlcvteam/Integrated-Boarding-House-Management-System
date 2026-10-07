@extends('layouts.admin')

@section('title', 'Maintenance Requests')
@section('page_title', 'Maintenance Requests')
@section('page_subtitle', 'Maintenance > Request List')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.maintenance.index') }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Maintenance List
    </a>
</div>

<div class="custom-card text-center py-5">
    <div class="empty-state-icon mx-auto text-primary mb-3" style="width: 64px; height: 64px; border-radius: 50%; background-color: var(--table-row-hover); display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-info-circle fs-1"></i>
    </div>
    <h4 class="fw-bold mb-2" style="color: var(--text-primary);">Tenant-Only Feature</h4>
    <p class="text-secondary mx-auto mb-4" style="max-width: 500px; font-size: 0.95rem;">
        Maintenance requests can only be submitted by tenants from their Tenant Portal.
        The Admin can review requests, assign personnel, and update the request status.
    </p>
    <a href="{{ route('admin.maintenance.index') }}" class="btn-primary-custom text-decoration-none px-4 py-2">
        <i class="bi bi-list-check me-1"></i> View All Maintenance Requests
    </a>
</div>
@endsection
