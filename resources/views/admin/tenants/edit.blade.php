@extends('layouts.admin')

@section('title', 'Edit Tenant: ' . $tenant->full_name)
@section('page_title', 'Edit Tenant')
@section('page_subtitle', 'Tenants > Edit Tenant')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Tenant Details
    </a>
</div>

<div class="custom-card" style="max-width: 800px;">
    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4 flex-wrap gap-2" style="border-color: var(--border-color) !important;">
        <div class="d-flex align-items-center gap-2">
            <div class="sidebar-brand-icon" style="width: 38px; height: 38px; font-size: 1.05rem;">
                <i class="bi bi-person-gear"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Edit Tenant Information</h5>
                <p class="text-secondary mb-0" style="font-size: 0.82rem;">Update core lease parameters for <strong>{{ $tenant->full_name }}</strong></p>
            </div>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-3 py-1 fw-bold">
            {{ $tenant->tenant_code ?? ('TEN-' . $tenant->id) }}
        </span>
    </div>

    <form action="{{ route('admin.tenants.update', $tenant->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- 1. Full Name -->
            <div class="col-12">
                <label for="full_name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="full_name" id="full_name" 
                           class="form-control border-start-0 ps-0 @error('full_name') is-invalid @enderror" 
                           value="{{ old('full_name', $tenant->full_name) }}" required placeholder="Enter tenant's full name">
                </div>
                @error('full_name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- 2. Contact Number -->
            <div class="col-md-6">
                <label for="contact_number" class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                        <i class="bi bi-telephone"></i>
                    </span>
                    <input type="text" name="contact_number" id="contact_number" 
                           class="form-control border-start-0 ps-0 @error('contact_number') is-invalid @enderror" 
                           value="{{ old('contact_number', $tenant->contact_number) }}" required placeholder="e.g. 09123456789">
                </div>
                @error('contact_number')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- 3. Assigned Room -->
            <div class="col-md-6">
                <label for="room_id" class="form-label fw-semibold">Assigned Room</label>
                <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror">
                    <option value="">Unassigned</option>
                    @foreach($availableRooms as $room)
                        <option value="{{ $room->id }}" {{ old('room_id', $tenant->room_id) == $room->id ? 'selected' : '' }}>
                            Room {{ $room->room_number }} ({{ $room->room_type }}) — ₱{{ number_format($room->monthly_rent, 2) }}/mo
                            {{ $tenant->room_id == $room->id ? '(Current Room)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('room_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- 4. Moved In Date -->
            <div class="col-md-6">
                <label for="move_in_date" class="form-label fw-semibold">Moved In Date <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                        <i class="bi bi-calendar-event"></i>
                    </span>
                    <input type="date" name="move_in_date" id="move_in_date" 
                           class="form-control border-start-0 ps-0 @error('move_in_date') is-invalid @enderror" 
                           value="{{ old('move_in_date', $tenant->move_in_date ? $tenant->move_in_date->format('Y-m-d') : '') }}" required>
                </div>
                <div class="form-text" style="font-size: 0.76rem; color: var(--text-secondary);">
                    Sets the official start of lease and billing anchor date.
                </div>
                @error('move_in_date')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- 5. Status -->
            <div class="col-md-6">
                <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status', $tenant->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ old('status', $tenant->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="inactive" {{ old('status', $tenant->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="moved_out" {{ old('status', $tenant->status) == 'moved_out' ? 'selected' : '' }}>Moved Out</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
            <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="btn-secondary-custom text-decoration-none">Cancel</a>
            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-check-lg"></i> Update Tenant
            </button>
        </div>
    </form>
</div>
@endsection
