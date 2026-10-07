@extends('layouts.tenant')

@section('title', 'Report Maintenance Issue')

@section('content')
<div class="mb-4">
    <a href="{{ route('tenant.maintenance.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Maintenance
    </a>
    <h1 class="h3 fw-bold mb-1">Report Maintenance Issue</h1>
    <p class="text-muted mb-0">Submit a repair request for your room. The Admin will review and assign staff to resolve it.</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">Maintenance Ticket Form</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.maintenance.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Room Info Box -->
                    <div class="p-3 bg-light rounded mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Location:</span>
                            <h6 class="fw-bold mb-0">Room {{ $tenant->room->room_number }} (Floor {{ $tenant->room->floor }})</h6>
                        </div>
                        <span class="badge bg-primary-subtle text-primary">Your Assigned Room</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">Issue Category <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                <option value="Plumbing" {{ old('category') === 'Plumbing' ? 'selected' : '' }}>Plumbing / Faucet / Toilet</option>
                                <option value="Electrical" {{ old('category') === 'Electrical' ? 'selected' : '' }}>Electrical / Outlet / Lighting</option>
                                <option value="Furniture" {{ old('category') === 'Furniture' ? 'selected' : '' }}>Furniture / Bed / Study Table</option>
                                <option value="Ventilation" {{ old('category') === 'Ventilation' ? 'selected' : '' }}>Fan / AC / Ventilation</option>
                                <option value="Structural" {{ old('category') === 'Structural' ? 'selected' : '' }}>Door / Lock / Window / Roof</option>
                                <option value="Other" {{ old('category') === 'Other' ? 'selected' : '' }}>Other Facilities</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="priority" class="form-label fw-semibold">Urgency / Priority <span class="text-danger">*</span></label>
                            <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low (Minor inconvenience)</option>
                                <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium (Standard repair needed)</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High (Affects daily living)</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent (Water leak, electrical hazard)</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="title" class="form-label fw-semibold">Issue Title / Summary <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="e.g. Leaking faucet in private bathroom" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Detailed Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Describe what happened, where the issue is, and any relevant details..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="attachment" class="form-label fw-semibold">Attach Photo of Issue (Optional)</label>
                            <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror" accept="image/*">
                            <div class="form-text small">Attaching a clear photo helps the Admin dispatch the right technician and replacement parts faster.</div>
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="{{ route('tenant.maintenance.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send me-1"></i> Submit Maintenance Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
