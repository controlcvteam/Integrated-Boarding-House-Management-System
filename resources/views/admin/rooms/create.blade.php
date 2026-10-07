@extends('layouts.admin')

@section('title', 'Add New Room')
@section('page_title', 'Add New Room')
@section('page_subtitle', 'Create a new room record')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.rooms.index') }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Room List
    </a>
</div>

<div class="custom-card">
    <div class="border-bottom pb-3 mb-4" style="border-color: var(--border-color) !important;">
        <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Room Information</h5>
        <p class="text-secondary mb-0" style="font-size: 0.85rem;">Fill in the details below to register a new room in the boarding house.</p>
    </div>

    <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <!-- Room Number -->
            <div class="col-md-4">
                <label for="room_number" class="form-label">Room Number <span class="text-danger">*</span></label>
                <input type="text" name="room_number" id="room_number" 
                       class="form-control @error('room_number') is-invalid @enderror" 
                       value="{{ old('room_number') }}" required placeholder="e.g. 101, 201, Room A">
                @error('room_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Room Name (Optional) -->
            <div class="col-md-4">
                <label for="room_name" class="form-label">Room Name (Optional)</label>
                <input type="text" name="room_name" id="room_name" 
                       class="form-control @error('room_name') is-invalid @enderror" 
                       value="{{ old('room_name') }}" placeholder="e.g. Sunrise Room, Deluxe Suite">
                @error('room_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Room Type -->
            <div class="col-md-4">
                <label for="room_type" class="form-label">Room Type <span class="text-danger">*</span></label>
                <select name="room_type" id="room_type" class="form-select @error('room_type') is-invalid @enderror" required>
                    <option value="">Select room type</option>
                    <option value="Single Room" {{ old('room_type') == 'Single Room' ? 'selected' : '' }}>Single Room</option>
                    <option value="Double Room" {{ old('room_type') == 'Double Room' ? 'selected' : '' }}>Double Room</option>
                    <option value="Deluxe Room" {{ old('room_type') == 'Deluxe Room' ? 'selected' : '' }}>Deluxe Room</option>
                    <option value="Family Room" {{ old('room_type') == 'Family Room' ? 'selected' : '' }}>Family Room</option>
                    <option value="Dormitory (4-Bed)" {{ old('room_type') == 'Dormitory (4-Bed)' ? 'selected' : '' }}>Dormitory (4-Bed)</option>
                </select>
                @error('room_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Capacity -->
            <div class="col-md-4">
                <label for="capacity" class="form-label">Capacity (Max Occupants) <span class="text-danger">*</span></label>
                <input type="number" name="capacity" id="capacity" min="1" 
                       class="form-control @error('capacity') is-invalid @enderror" 
                       value="{{ old('capacity', 1) }}" required>
                @error('capacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Monthly Rental Fee -->
            <div class="col-md-4">
                <label for="monthly_rent" class="form-label">Monthly Rental Fee (₱) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">₱</span>
                    <input type="number" step="0.01" name="monthly_rent" id="monthly_rent" 
                           class="form-control border-start-0 ps-0 @error('monthly_rent') is-invalid @enderror" 
                           value="{{ old('monthly_rent') }}" required placeholder="0.00">
                </div>
                @error('monthly_rent')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Floor -->
            <div class="col-md-4">
                <label for="floor" class="form-label">Floor</label>
                <input type="text" name="floor" id="floor" 
                       class="form-control @error('floor') is-invalid @enderror" 
                       value="{{ old('floor') }}" placeholder="e.g. 1st Floor, 2nd Floor">
                @error('floor')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Manual Availability Toggle -->
            <div class="col-md-12">
                <div class="form-check form-switch p-2 rounded" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="manual_available" id="manual_available" value="1" {{ old('manual_available', '1') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="manual_available" style="color: var(--text-primary); font-size: 0.9rem;">
                        Mark as Available for Tenant Booking / Room Requests
                    </label>
                    <div class="text-secondary ps-4" style="font-size: 0.78rem;">
                        Uncheck if this room is currently closed for maintenance, repair, or reserved by owner decision.
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="col-md-12">
                <label for="description" class="form-label">Description (Optional)</label>
                <textarea name="description" id="description" rows="3" 
                          class="form-control @error('description') is-invalid @enderror" 
                          placeholder="Describe room features, ventilation, windows, etc.">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Amenities -->
            <div class="col-md-12">
                <label class="form-label">Amenities</label>
                <div class="row g-2 p-3 rounded" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
                    @foreach($standardAmenities as $amenity)
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity }}" 
                                       id="amenity_{{ $loop->index }}" 
                                       {{ is_array(old('amenities')) && in_array($amenity, old('amenities')) ? 'checked' : '' }}>
                                <label class="form-check-label" for="amenity_{{ $loop->index }}" style="font-size: 0.875rem;">
                                    {{ $amenity }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Room Photos Upload -->
            <div class="col-md-12">
                <label for="images" class="form-label">Upload Room Photos (Optional)</label>
                <input type="file" name="images[]" id="images" multiple 
                       class="form-control @error('images.*') is-invalid @enderror" 
                       accept="image/jpeg,image/png,image/webp">
                <div class="form-text" style="font-size: 0.78rem; color: var(--text-secondary);">
                    Allowed formats: JPG, JPEG, PNG, WEBP. Max 5MB each. First uploaded photo will be set as Primary.
                </div>
                @error('images.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
            <a href="{{ route('admin.rooms.index') }}" class="btn-secondary-custom text-decoration-none">Cancel</a>
            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-save"></i> Save Room
            </button>
        </div>
    </form>
</div>
@endsection
