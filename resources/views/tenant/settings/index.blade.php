@extends('layouts.tenant')

@section('title', 'Tenant Settings & Profile')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">My Account Settings</h1>
    <p class="text-muted mb-0">Update your personal contact details, emergency information, and account password.</p>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <!-- Personal & Emergency Contact Form -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-person-circle text-primary me-2"></i> Profile & Emergency Contacts
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.settings.profile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Profile Photo Upload -->
                    <div class="mb-4 d-flex align-items-center gap-3 p-3 rounded" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
                        <div class="position-relative flex-shrink-0">
                            <img id="avatar-preview-img" src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" class="rounded-circle border shadow-sm" style="width: 76px; height: 76px; object-fit: cover;">
                        </div>
                        <div class="flex-grow-1">
                            <label for="profile_picture" class="form-label fw-semibold mb-1">Profile Photo</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control form-control-sm @error('profile_picture') is-invalid @enderror" accept="image/*" onchange="previewAvatar(this)">
                            <div class="form-text small text-secondary" style="font-size: 0.76rem;">Upload JPG, PNG, or WEBP. Max 5MB.</div>
                            @error('profile_picture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" id="email" class="form-control bg-light" value="{{ $user->email }}" disabled readonly>
                        <div class="form-text small">Email cannot be changed directly. Contact the Admin if you need to update it.</div>
                    </div>

                    <div class="mb-3">
                        <label for="contact_number" class="form-label fw-semibold">Mobile Number</label>
                        <input type="text" name="contact_number" id="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number', $user->contact_number) }}" placeholder="e.g. 09171234567">
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label for="gender" class="form-label fw-semibold">Gender</label>
                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">-- Select Gender --</option>
                                <option value="Male" {{ old('gender', $tenant->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $tenant->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $tenant->gender ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label for="date_of_birth" class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', ($tenant && $tenant->date_of_birth) ? $tenant->date_of_birth->format('Y-m-d') : '') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">Permanent Home Address</label>
                        <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-telephone-plus me-1"></i> Emergency Contact Information</h6>

                    <div class="mb-3">
                        <label for="emergency_contact_name" class="form-label fw-semibold">Contact Person Name</label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" value="{{ old('emergency_contact_name', $tenant->emergency_contact_name ?? '') }}" placeholder="Parent / Guardian / Spouse">
                        @error('emergency_contact_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="emergency_contact_number" class="form-label fw-semibold">Emergency Phone Number</label>
                        <input type="text" name="emergency_contact_number" id="emergency_contact_number" class="form-control @error('emergency_contact_number') is-invalid @enderror" value="{{ old('emergency_contact_number', $tenant->emergency_contact_number ?? '') }}" placeholder="e.g. 09181234567">
                        @error('emergency_contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <!-- Change Password Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-shield-lock text-primary me-2"></i> Change Password
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.settings.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        <div class="form-text small">Minimum 8 characters.</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-key me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview-img');
            if (preview) {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
