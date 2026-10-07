@extends('layouts.admin')

@section('title', 'Edit Room ' . $room->room_number)
@section('page_title', 'Edit Room')
@section('page_subtitle', 'Update details for Room ' . $room->room_number)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.rooms.show', $room->id) }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Room Details
    </a>
</div>

<div class="custom-card">
    <div class="border-bottom pb-3 mb-4" style="border-color: var(--border-color) !important;">
        <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Edit Room {{ $room->room_number }}</h5>
        <p class="text-secondary mb-0" style="font-size: 0.85rem;">Modify room specifications, rates, amenities, and availability.</p>
    </div>

    <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Room Number -->
            <div class="col-md-4">
                <label for="room_number" class="form-label">Room Number <span class="text-danger">*</span></label>
                <input type="text" name="room_number" id="room_number" 
                       class="form-control @error('room_number') is-invalid @enderror" 
                       value="{{ old('room_number', $room->room_number) }}" required>
                @error('room_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Room Name (Optional) -->
            <div class="col-md-4">
                <label for="room_name" class="form-label">Room Name (Optional)</label>
                <input type="text" name="room_name" id="room_name" 
                       class="form-control @error('room_name') is-invalid @enderror" 
                       value="{{ old('room_name', $room->room_name) }}">
                @error('room_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Room Type -->
            <div class="col-md-4">
                <label for="room_type" class="form-label">Room Type <span class="text-danger">*</span></label>
                <select name="room_type" id="room_type" class="form-select @error('room_type') is-invalid @enderror" required>
                    <option value="Single Room" {{ old('room_type', $room->room_type) == 'Single Room' ? 'selected' : '' }}>Single Room</option>
                    <option value="Double Room" {{ old('room_type', $room->room_type) == 'Double Room' ? 'selected' : '' }}>Double Room</option>
                    <option value="Deluxe Room" {{ old('room_type', $room->room_type) == 'Deluxe Room' ? 'selected' : '' }}>Deluxe Room</option>
                    <option value="Family Room" {{ old('room_type', $room->room_type) == 'Family Room' ? 'selected' : '' }}>Family Room</option>
                    <option value="Dormitory (4-Bed)" {{ old('room_type', $room->room_type) == 'Dormitory (4-Bed)' ? 'selected' : '' }}>Dormitory (4-Bed)</option>
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
                       value="{{ old('capacity', $room->capacity) }}" required>
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
                           value="{{ old('monthly_rent', $room->monthly_rent) }}" required>
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
                       value="{{ old('floor', $room->floor) }}">
                @error('floor')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Manual Availability Toggle -->
            <div class="col-md-12">
                <div class="form-check form-switch p-2 rounded" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="manual_available" id="manual_available" value="1" 
                           {{ old('manual_available', $room->manual_available) ? 'checked' : '' }}>
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
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $room->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Amenities -->
            <div class="col-md-12">
                <label class="form-label">Amenities</label>
                <div class="row g-2 p-3 rounded" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
                    @php $roomAmenities = (array) ($room->amenities ?? []); @endphp
                    @foreach($standardAmenities as $amenity)
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity }}" 
                                       id="amenity_{{ $loop->index }}" 
                                       {{ in_array($amenity, old('amenities', $roomAmenities)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="amenity_{{ $loop->index }}" style="font-size: 0.875rem;">
                                    {{ $amenity }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Upload Additional Photos -->
            <div class="col-md-12">
                <label for="images" class="form-label">Upload Additional Photos</label>
                <input type="file" name="images[]" id="images" multiple 
                       class="form-control @error('images.*') is-invalid @enderror" 
                       accept="image/jpeg,image/png,image/webp">
                <div class="form-text" style="font-size: 0.78rem; color: var(--text-secondary);">
                    Allowed formats: JPG, JPEG, PNG, WEBP. Max 5MB each.
                </div>
                @error('images.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
            <a href="{{ route('admin.rooms.show', $room->id) }}" class="btn-secondary-custom text-decoration-none">Cancel</a>
            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-check-lg"></i> Update Room
            </button>
        </div>
    </form>
</div>
@endsection
