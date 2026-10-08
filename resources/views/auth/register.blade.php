@extends('layouts.auth')

@section('title', 'Register as Tenant')
@section('card_width', '880px')

@section('content')
<div class="mb-4 text-center">
    <h3 class="fw-bold mb-1" style="font-size: 1.35rem; color: var(--text-primary);">Tenant Registration</h3>
    <p class="text-secondary" style="font-size: 0.88rem;">Create your account to request and secure a boarding house room</p>
</div>

<div class="alert alert-info py-2 px-3 mb-4 d-flex align-items-center gap-2" style="font-size: 0.85rem; border-radius: 8px;">
    <i class="bi bi-info-circle-fill fs-5 text-info"></i>
    <div>New registrations are automatically submitted as <strong>Pending</strong> for Admin review and approval.</div>
</div>

<form action="{{ route('register') }}" method="POST">
    @csrf

    <div class="row g-3">
        <!-- Personal Information Section Header -->
        <div class="col-12 pb-1 border-bottom" style="border-color: var(--border-color) !important;">
            <h6 class="fw-bold mb-0" style="color: var(--text-primary);">
                <i class="bi bi-person-badge me-1"></i> Personal Information
            </h6>
        </div>

        <!-- Full Name -->
        <div class="col-md-12">
            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name') }}" required placeholder="e.g. Juan Dela Cruz">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="col-md-6">
            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" required placeholder="juan@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Contact Number -->
        <div class="col-md-6">
            <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
            <input type="text" name="contact_number" id="contact_number" 
                   class="form-control @error('contact_number') is-invalid @enderror" 
                   value="{{ old('contact_number') }}" required placeholder="e.g. 09123456789">
            @error('contact_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Address -->
        <div class="col-md-12">
            <label for="address" class="form-label">Complete Home Address <span class="text-danger">*</span></label>
            <textarea name="address" id="address" rows="2" 
                      class="form-control @error('address') is-invalid @enderror" 
                      required placeholder="House No., Street, Barangay, Municipality/City, Province">{{ old('address') }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Gender -->
        <div class="col-md-6">
            <label for="gender" class="form-label">Gender</label>
            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                <option value="">Select gender</option>
                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Date of Birth -->
        <div class="col-md-6">
            <label for="date_of_birth" class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" id="date_of_birth" 
                   class="form-control @error('date_of_birth') is-invalid @enderror" 
                   value="{{ old('date_of_birth') }}">
            @error('date_of_birth')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Room Selection Section Header -->
        <div class="col-12 pt-3 pb-1 border-bottom d-flex justify-content-between align-items-center" style="border-color: var(--border-color) !important;">
            <h6 class="fw-bold mb-0" style="color: var(--text-primary);">
                <i class="bi bi-door-open me-1"></i> Browse & Select Preferred Room
            </h6>
            <a href="{{ route('rooms.public') }}" target="_blank" class="small fw-semibold text-decoration-none" style="color: #38bdf8; font-size: 0.8rem;">
                <i class="bi bi-window-fullscreen me-1"></i> Open Full Room Catalog
            </a>
        </div>

        <!-- Room Selector Arrangement -->
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <p class="text-secondary small mb-0">
                    Choose a room below to request immediate reservation upon account approval, or select "Decide Later".
                </p>
                <div class="input-group input-group-sm" style="max-width: 280px;">
                    <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="registerRoomSearchInput" class="form-control border-start-0" 
                           placeholder="Filter rooms (e.g. 002, single, 5000)..." oninput="filterRegisterRooms(this.value)">
                </div>
            </div>

            <div class="row g-2 mb-3" id="registerRoomCardsContainer" style="max-height: 320px; overflow-y: auto; padding-right: 4px;">
                <!-- Option: Decide Later -->
                <div class="col-md-6 register-room-card-col" data-room-search="decide later no preference any">
                    <label class="room-select-card d-flex align-items-center gap-3 p-3 rounded border h-100 position-relative cursor-pointer" 
                           style="border-color: var(--border-color); background-color: var(--table-header-bg); cursor: pointer;">
                        <input type="radio" name="preferred_room_radio" value="" class="form-check-input mt-0" 
                               {{ empty(old('preferred_room_id', $selectedRoomId ?? null)) ? 'checked' : '' }}
                                onchange="onRoomRadioChanged(this)">
                        <div>
                            <div class="fw-bold small" style="color: var(--text-primary);">Decide Later / No Preference</div>
                            <div class="text-secondary" style="font-size: 0.76rem;">You can browse and request rooms after registration.</div>
                        </div>
                    </label>
                </div>

                @forelse($availableRooms as $room)
                    @php
                        $isSelected = old('preferred_room_id', $selectedRoomId ?? null) == $room->id;
                        $searchKeywords = strtolower($room->room_number . ' ' . $room->room_name . ' ' . $room->room_type . ' floor ' . $room->floor . ' ' . (int)$room->monthly_rent . ' ' . $room->monthly_rent . ' available ' . (is_array($room->amenities) ? implode(' ', $room->amenities) : ''));
                    @endphp
                    <div class="col-md-6 register-room-card-col" data-room-search="{{ $searchKeywords }}">
                        <label class="room-select-card d-flex align-items-center gap-3 p-3 rounded border h-100 position-relative cursor-pointer {{ $isSelected ? 'border-primary' : '' }}" 
                               style="border-color: {{ $isSelected ? '#38bdf8' : 'var(--border-color)' }}; background-color: var(--bg-card); cursor: pointer; transition: all 0.2s ease;">
                            <input type="radio" name="preferred_room_radio" value="{{ $room->id }}" class="form-check-input mt-0" 
                                   {{ $isSelected ? 'checked' : '' }}
                                   onchange="onRoomRadioChanged(this)">
                            
                            <div class="rounded overflow-hidden flex-shrink-0" style="width: 50px; height: 50px; background-color: var(--table-header-bg);">
                                @if($room->primaryImage)
                                    <img src="{{ asset('storage/' . $room->primaryImage->image_path) }}" alt="Room {{ $room->room_number }}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('images/room-placeholder.svg') }}';">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                        <i class="bi bi-door-open fs-4"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="small" style="color: var(--text-primary);">Room {{ $room->room_number }}</strong>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">
                                        {{ $room->available_slots }} slots
                                    </span>
                                </div>
                                <div class="text-secondary text-truncate" style="font-size: 0.76rem;">{{ $room->room_type }} &bull; Floor {{ $room->floor }}</div>
                                <div class="fw-bold text-success" style="font-size: 0.82rem;">₱{{ number_format($room->monthly_rent, 2) }}<span class="fw-normal text-muted" style="font-size: 0.72rem;">/mo</span></div>
                            </div>
                        </label>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="p-3 text-center text-muted border rounded" style="font-size: 0.85rem;">
                            No rooms are currently marked available. You can register now and wait for new vacancy.
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Hidden / Synced Room ID field -->
            <input type="hidden" name="preferred_room_id" id="preferred_room_id" value="{{ old('preferred_room_id', $selectedRoomId ?? '') }}">

            <!-- Fallback Select Dropdown for Accessibility -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-secondary small">Or select via list:</span>
                <select id="preferred_room_select" class="form-select form-select-sm" style="max-width: 320px;" onchange="onRoomSelectChanged(this)">
                    <option value="">No preference (Decide later)</option>
                    @foreach($availableRooms as $room)
                        <option value="{{ $room->id }}" {{ old('preferred_room_id', $selectedRoomId ?? '') == $room->id ? 'selected' : '' }}>
                            Room {{ $room->room_number }} ({{ $room->room_type }}) — ₱{{ number_format($room->monthly_rent, 2) }}/mo
                        </option>
                    @endforeach
                </select>
            </div>
            @error('preferred_room_id')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Security Section Header -->
        <div class="col-12 pt-3 pb-1 border-bottom" style="border-color: var(--border-color) !important;">
            <h6 class="fw-bold mb-0" style="color: var(--text-primary);">
                <i class="bi bi-shield-lock me-1"></i> Account Security
            </h6>
        </div>

        <!-- Password -->
        <div class="col-md-6">
            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" id="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   required placeholder="Minimum 8 characters">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="col-md-6">
            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" id="password_confirmation" 
                   class="form-control" required placeholder="Re-type password">
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn-primary-custom w-100 justify-content-center py-2 fs-6 mb-3">
            <i class="bi bi-person-check-fill"></i> Register Account
        </button>
    </div>
</form>

<div class="text-center pt-2 border-top" style="border-color: var(--border-color) !important;">
    <p class="text-secondary mb-0" style="font-size: 0.875rem;">
        Already registered? 
        <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: #38bdf8;">
            Back to Login
        </a>
    </p>
</div>

<script>
    function onRoomRadioChanged(radio) {
        const val = radio.value;
        document.getElementById('preferred_room_id').value = val;
        document.getElementById('preferred_room_select').value = val;

        // Visual feedback
        document.querySelectorAll('.room-select-card').forEach(card => {
            card.style.borderColor = 'var(--border-color)';
        });
        const parentLabel = radio.closest('label');
        if (parentLabel && val) {
            parentLabel.style.borderColor = '#38bdf8';
        }
    }

    function onRoomSelectChanged(select) {
        const val = select.value;
        document.getElementById('preferred_room_id').value = val;

        // Sync radio
        const radios = document.querySelectorAll('input[name="preferred_room_radio"]');
        radios.forEach(r => {
            r.checked = (r.value === val);
            const parentLabel = r.closest('label');
            if (parentLabel) {
                parentLabel.style.borderColor = (r.value === val && val) ? '#38bdf8' : 'var(--border-color)';
            }
        });
    }

    function filterRegisterRooms(query) {
        const q = query.trim().toLowerCase();
        const cards = document.querySelectorAll('.register-room-card-col');
        cards.forEach(card => {
            const text = (card.getAttribute('data-room-search') || '').toLowerCase();
            if (!q || text.includes(q)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
