@extends('layouts.admin')

@section('title', 'System Settings & Profile')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">System & Profile Settings</h1>
    <p class="text-muted mb-0">Manage your Admin profile, security credentials, and GCash payment configurations.</p>
</div>

<div class="row g-4">
    <!-- Admin Profile Card -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4 h-100">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-person-circle text-primary me-2"></i> Admin Profile
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.profile') }}" method="POST" enctype="multipart/form-data">
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
                            <div class="form-text small text-secondary" style="font-size: 0.76rem;">Upload JPG, PNG, WEBP, or SVG. Max 5MB.</div>
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
                        <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contact_number" class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number', $user->contact_number) }}" placeholder="e.g. 09171234567">
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">Boarding House Physical Address</label>
                        <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="Address displayed on tenant receipts...">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle me-1"></i> Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Security & Password Card -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4 h-100">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-shield-lock text-primary me-2"></i> Change Password
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.password') }}" method="POST">
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
                        <div class="form-text small">Minimum 8 characters. Use a strong combination of letters and numbers.</div>
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

    <!-- GCash Payment & QR Code Settings Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-qr-code text-primary me-2"></i> GCash Payment & QR Code Settings
                </h5>
                <span class="badge bg-primary-subtle text-primary">Configured for Tenant GCash Payments</span>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-4">
                    Change the official GCash receiver account name, mobile number, and upload a new GCash QR Code. These details will be dynamically presented to tenants when they scan and submit their rent payments.
                </p>

                <form action="{{ route('admin.settings.gcash') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gcash_name" class="form-label fw-semibold">GCash Account / Receiver Name <span class="text-danger">*</span></label>
                                <input type="text" name="gcash_name" id="gcash_name" class="form-control @error('gcash_name') is-invalid @enderror" value="{{ old('gcash_name', $gcashName) }}" required placeholder="e.g. Admin / Property Owner">
                                @error('gcash_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="gcash_number" class="form-label fw-semibold">GCash Mobile / Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="gcash_number" id="gcash_number" class="form-control font-monospace @error('gcash_number') is-invalid @enderror" value="{{ old('gcash_number', $gcashNumber) }}" required placeholder="e.g. 0917-888-9999">
                                @error('gcash_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="gcash_qr_code" class="form-label fw-semibold">Upload New GCash QR Code Image</label>
                                <input type="file" name="gcash_qr_code" id="gcash_qr_code" class="form-control @error('gcash_qr_code') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml" onchange="previewGcashQr(this)">
                                <div class="form-text small">Accepted formats: JPG, PNG, WEBP, SVG. Max file size: 5MB.</div>
                                @error('gcash_qr_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Current & Preview QR Display -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-center align-items-center text-center">
                                <div class="d-flex align-items-center justify-content-center gap-4 flex-wrap mb-3">
                                    <div>
                                        <span class="small fw-semibold text-muted d-block mb-2">Current Active QR:</span>
                                        <img id="current-qr-img" src="{{ asset('storage/' . ($gcashQrPath && $gcashQrPath !== '0' ? $gcashQrPath : 'settings/default-gcash-qr.svg')) }}?v=2" alt="Current GCash QR" class="img-thumbnail bg-white shadow-sm" style="max-height: 150px; max-width: 150px; object-fit: contain;" onerror="this.onerror=null; this.src='{{ asset('storage/settings/default-gcash-qr.svg') }}';">
                                    </div>
                                    <div id="preview-qr-wrapper" class="d-none">
                                        <span class="small fw-semibold text-success d-block mb-2">New QR Preview:</span>
                                        <img id="new-qr-preview" src="#" alt="New QR Preview" class="img-thumbnail border-success shadow-sm" style="max-height: 150px; max-width: 150px; object-fit: contain;">
                                    </div>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-info-circle text-primary me-1"></i>
                                    When changed, tenants will immediately see this updated QR code and receiver info when paying rent online.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Save GCash Settings
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

function previewGcashQr(input) {
    const wrapper = document.getElementById('preview-qr-wrapper');
    const previewImg = document.getElementById('new-qr-preview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            wrapper.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        wrapper.classList.add('d-none');
    }
}
</script>
@endsection
