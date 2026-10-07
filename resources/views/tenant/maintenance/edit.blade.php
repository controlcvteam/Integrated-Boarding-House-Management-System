@extends('layouts.tenant')

@section('title', 'Edit Maintenance Ticket #' . str_pad($request->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="mb-4">
    <a href="{{ route('tenant.maintenance.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Maintenance
    </a>
    <h1 class="h3 fw-bold mb-1">Edit Maintenance Issue</h1>
    <p class="text-muted mb-0">Update your repair request details or upload a new photo.</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Edit Maintenance Ticket #MNT-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</h5>
                <span class="badge bg-secondary-subtle text-secondary font-monospace">Status: {{ ucfirst($request->status) }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.maintenance.update', $request->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Room Info Box -->
                    <div class="p-3 bg-light rounded mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Location:</span>
                            <h6 class="fw-bold mb-0">Room {{ $request->room->room_number ?? $tenant->room->room_number ?? 'Assigned Room' }}</h6>
                        </div>
                        <span class="badge bg-primary-subtle text-primary">Your Accommodation</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">Issue Category <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                <option value="Plumbing" {{ old('category', $request->category) === 'Plumbing' ? 'selected' : '' }}>Plumbing / Faucet / Toilet</option>
                                <option value="Electrical" {{ old('category', $request->category) === 'Electrical' ? 'selected' : '' }}>Electrical / Outlet / Lighting</option>
                                <option value="Furniture" {{ old('category', $request->category) === 'Furniture' ? 'selected' : '' }}>Furniture / Bed / Study Table</option>
                                <option value="Ventilation" {{ old('category', $request->category) === 'Ventilation' ? 'selected' : '' }}>Fan / AC / Ventilation</option>
                                <option value="Structural" {{ old('category', $request->category) === 'Structural' ? 'selected' : '' }}>Door / Lock / Window / Roof</option>
                                <option value="Other" {{ old('category', $request->category) === 'Other' ? 'selected' : '' }}>Other Facilities</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="priority" class="form-label fw-semibold">Urgency / Priority <span class="text-danger">*</span></label>
                            <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="low" {{ old('priority', strtolower($request->priority)) === 'low' ? 'selected' : '' }}>Low (Minor inconvenience)</option>
                                <option value="medium" {{ old('priority', strtolower($request->priority)) === 'medium' ? 'selected' : '' }}>Medium (Standard repair needed)</option>
                                <option value="high" {{ old('priority', strtolower($request->priority)) === 'high' ? 'selected' : '' }}>High (Affects daily living)</option>
                                <option value="urgent" {{ old('priority', strtolower($request->priority)) === 'urgent' ? 'selected' : '' }}>Urgent (Water leak, electrical hazard)</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="title" class="form-label fw-semibold">Issue Title / Summary <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="e.g. Leaking faucet in private bathroom" value="{{ old('title', $request->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Detailed Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Describe what happened, where the issue is, and any relevant details..." required>{{ old('description', $request->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="attachment" class="form-label fw-semibold">Update Photo (Optional)</label>
                            @if($request->attachment_path)
                                <div class="mb-2 p-2 bg-light rounded d-flex align-items-center gap-3">
                                    <img src="{{ asset('storage/' . $request->attachment_path) }}" alt="Current attachment" class="img-thumbnail" style="max-height: 80px;">
                                    <div>
                                        <span class="small fw-semibold d-block">Current Attachment</span>
                                        <span class="text-muted small">Choose a new file below only if you want to replace this photo.</span>
                                    </div>
                                </div>
                            @endif
                            <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror" accept="image/*">
                            <div class="form-text small">Accepted formats: JPG, JPEG, PNG, WEBP. Max size: 5MB.</div>
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <a href="{{ route('tenant.maintenance.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2-circle me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
