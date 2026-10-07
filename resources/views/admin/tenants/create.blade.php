@extends('layouts.admin')

@section('title', 'Add Tenant')
@section('page_title', 'Add Tenant')
@section('page_subtitle', 'Tenants > Add Tenant')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.tenants.index') }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Tenant List
    </a>
</div>

<div class="custom-card">
    <div class="d-flex align-items-center gap-2 border-bottom pb-3 mb-4" style="border-color: var(--border-color) !important;">
        <div class="sidebar-brand-icon" style="width: 36px; height: 36px; font-size: 1rem;">
            <i class="bi bi-person-plus-fill"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Add New Tenant</h5>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">Fill in the details below to register a new tenant.</p>
        </div>
    </div>

    <form action="{{ route('admin.tenants.store') }}" method="POST">
        @csrf

        <div class="row g-3">
            <!-- Full Name -->
            <div class="col-md-12">
                <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="full_name" id="full_name" 
                       class="form-control @error('full_name') is-invalid @enderror" 
                       value="{{ old('full_name') }}" required placeholder="Enter full name">
                @error('full_name')
                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <!-- Contact Number -->
            <div class="col-md-6">
                <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                <input type="text" name="contact_number" id="contact_number" 
                       class="form-control @error('contact_number') is-invalid @enderror" 
                       value="{{ old('contact_number') }}" required placeholder="Enter contact number (e.g. 09123456789)">
                @error('contact_number')
                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="col-md-6">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" required placeholder="Enter email address">
                @error('email')
                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <!-- Room Number -->
            <div class="col-md-6">
                <label for="room_id" class="form-label">Room Number <span class="text-danger">*</span></label>
                <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                    <option value="">Select room number</option>
                    @foreach($availableRooms as $room)
                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                            Room {{ $room->room_number }} ({{ $room->room_type }}) — ₱{{ number_format($room->monthly_rent, 2) }}/mo [{{ $room->available_slots }} slots available]
                        </option>
                    @endforeach
                </select>
                @error('room_id')
                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <!-- Gender -->
            <div class="col-md-6">
                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                    <option value="">Select gender</option>
                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <!-- Date of Birth -->
            <div class="col-md-6">
                <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" name="date_of_birth" id="date_of_birth" 
                       class="form-control @error('date_of_birth') is-invalid @enderror" 
                       value="{{ old('date_of_birth') }}" required>
                @error('date_of_birth')
                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <!-- Move-in Date -->
            <div class="col-md-6">
                <label for="move_in_date" class="form-label">Move-in Date <span class="text-danger">*</span></label>
                <input type="date" name="move_in_date" id="move_in_date" 
                       class="form-control @error('move_in_date') is-invalid @enderror" 
                       value="{{ old('move_in_date', date('Y-m-d')) }}" required>
                <div class="form-text" style="font-size: 0.78rem;">
                    This date determines the tenant's monthly rent due date.
                </div>
                @error('move_in_date')
                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <!-- Address -->
            <div class="col-md-12">
                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                <textarea name="address" id="address" rows="2" 
                          class="form-control @error('address') is-invalid @enderror" 
                          required placeholder="Enter complete address">{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <!-- Status -->
            <div class="col-md-4">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="">Select status</option>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="moved_out" {{ old('status') == 'moved_out' ? 'selected' : '' }}>Moved Out</option>
                </select>
                @error('status')
                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <!-- Nationality -->
            <div class="col-md-4">
                <label for="nationality" class="form-label">Nationality</label>
                <input type="text" name="nationality" id="nationality" class="form-control" 
                       value="{{ old('nationality', 'Filipino') }}" placeholder="e.g. Filipino">
            </div>

            <!-- Emergency Contact -->
            <div class="col-md-4">
                <label for="emergency_contact" class="form-label">Emergency Contact (Optional)</label>
                <input type="text" name="emergency_contact" id="emergency_contact" class="form-control" 
                       value="{{ old('emergency_contact') }}" placeholder="e.g. Maria Dela Cruz (09171234567)">
            </div>

            <!-- Password -->
            <div class="col-md-12">
                <label for="password" class="form-label">Initial Account Password</label>
                <input type="text" name="password" id="password" class="form-control" 
                       value="{{ old('password', 'password') }}" placeholder="Default: password">
                <div class="form-text" style="font-size: 0.78rem;">
                    Default is "password". The tenant can change their password anytime from Settings.
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
            <a href="{{ route('admin.tenants.index') }}" class="btn-secondary-custom text-decoration-none">Cancel</a>
            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-save"></i> Save Tenant
            </button>
        </div>
    </form>
</div>
@endsection
