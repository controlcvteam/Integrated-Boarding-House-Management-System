@extends('layouts.admin')

@section('title', 'Record Payment')
@section('page_title', 'Record Payment')
@section('page_subtitle', 'Payments > Record Payment')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.payments.index') }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Payment List
    </a>
</div>

<div class="custom-card">
    <div class="border-bottom pb-3 mb-4" style="border-color: var(--border-color) !important;">
        <div class="d-flex align-items-center gap-2">
            <div class="sidebar-brand-icon" style="width: 36px; height: 36px; font-size: 1rem;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Record Payment</h5>
                <p class="text-secondary mb-0" style="font-size: 0.85rem;">Select any tenant to record and verify their rental payment (Cash or GCash).</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.payments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <!-- Tenant Select (All Tenants) -->
            <div class="col-md-6">
                <label for="tenant_id" class="form-label fw-semibold">Select Tenant <span class="text-danger">*</span></label>
                <select name="tenant_id" id="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required onchange="onTenantChanged(this)">
                    <option value="">-- Choose Tenant to Pay --</option>
                    @if(isset($groupedTenants))
                        @if($groupedTenants['who_pay']->isNotEmpty())
                            <optgroup label="📌 Tenants Who Need to Pay (Rent Due / Unpaid)">
                                @foreach($groupedTenants['who_pay'] as $t)
                                    @php
                                        $roomLabel = $t->room ? 'Room ' . $t->room->room_number . ' (' . $t->room->room_type . ')' : 'No room assigned';
                                        $statusLabel = ucfirst(str_replace('_', ' ', $t->status));
                                    @endphp
                                    <option value="{{ $t->id }}" 
                                            data-room="{{ $roomLabel }}"
                                            data-rent="{{ $t->room ? $t->room->monthly_rent : 0 }}"
                                            data-has-room="{{ $t->room ? '1' : '0' }}"
                                            {{ old('tenant_id', $selectedTenant ? $selectedTenant->id : '') == $t->id ? 'selected' : '' }}>
                                        {{ $t->full_name }} ({{ $t->tenant_code ?? ('TEN-' . $t->id) }}) — {{ $roomLabel }} [{{ $statusLabel }}]
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($groupedTenants['pending']->isNotEmpty())
                            <optgroup label="⏳ Tenants with Pending Verification">
                                @foreach($groupedTenants['pending'] as $t)
                                    @php
                                        $roomLabel = $t->room ? 'Room ' . $t->room->room_number . ' (' . $t->room->room_type . ')' : 'No room assigned';
                                        $statusLabel = ucfirst(str_replace('_', ' ', $t->status));
                                    @endphp
                                    <option value="{{ $t->id }}" 
                                            data-room="{{ $roomLabel }}"
                                            data-rent="{{ $t->room ? $t->room->monthly_rent : 0 }}"
                                            data-has-room="{{ $t->room ? '1' : '0' }}"
                                            {{ old('tenant_id', $selectedTenant ? $selectedTenant->id : '') == $t->id ? 'selected' : '' }}>
                                        {{ $t->full_name }} ({{ $t->tenant_code ?? ('TEN-' . $t->id) }}) — {{ $roomLabel }} [{{ $statusLabel }}]
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($groupedTenants['partial']->isNotEmpty())
                            <optgroup label="🌗 Tenants with Partial Payments">
                                @foreach($groupedTenants['partial'] as $t)
                                    @php
                                        $roomLabel = $t->room ? 'Room ' . $t->room->room_number . ' (' . $t->room->room_type . ')' : 'No room assigned';
                                        $statusLabel = ucfirst(str_replace('_', ' ', $t->status));
                                    @endphp
                                    <option value="{{ $t->id }}" 
                                            data-room="{{ $roomLabel }}"
                                            data-rent="{{ $t->room ? $t->room->monthly_rent : 0 }}"
                                            data-has-room="{{ $t->room ? '1' : '0' }}"
                                            {{ old('tenant_id', $selectedTenant ? $selectedTenant->id : '') == $t->id ? 'selected' : '' }}>
                                        {{ $t->full_name }} ({{ $t->tenant_code ?? ('TEN-' . $t->id) }}) — {{ $roomLabel }} [{{ $statusLabel }}]
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($groupedTenants['rejected']->isNotEmpty())
                            <optgroup label="❌ Tenants with Rejected Payments">
                                @foreach($groupedTenants['rejected'] as $t)
                                    @php
                                        $roomLabel = $t->room ? 'Room ' . $t->room->room_number . ' (' . $t->room->room_type . ')' : 'No room assigned';
                                        $statusLabel = ucfirst(str_replace('_', ' ', $t->status));
                                    @endphp
                                    <option value="{{ $t->id }}" 
                                            data-room="{{ $roomLabel }}"
                                            data-rent="{{ $t->room ? $t->room->monthly_rent : 0 }}"
                                            data-has-room="{{ $t->room ? '1' : '0' }}"
                                            {{ old('tenant_id', $selectedTenant ? $selectedTenant->id : '') == $t->id ? 'selected' : '' }}>
                                        {{ $t->full_name }} ({{ $t->tenant_code ?? ('TEN-' . $t->id) }}) — {{ $roomLabel }} [{{ $statusLabel }}]
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($groupedTenants['paid']->isNotEmpty())
                            <optgroup label="✅ Fully Paid Tenants">
                                @foreach($groupedTenants['paid'] as $t)
                                    @php
                                        $roomLabel = $t->room ? 'Room ' . $t->room->room_number . ' (' . $t->room->room_type . ')' : 'No room assigned';
                                        $statusLabel = ucfirst(str_replace('_', ' ', $t->status));
                                    @endphp
                                    <option value="{{ $t->id }}" 
                                            data-room="{{ $roomLabel }}"
                                            data-rent="{{ $t->room ? $t->room->monthly_rent : 0 }}"
                                            data-has-room="{{ $t->room ? '1' : '0' }}"
                                            {{ old('tenant_id', $selectedTenant ? $selectedTenant->id : '') == $t->id ? 'selected' : '' }}>
                                        {{ $t->full_name }} ({{ $t->tenant_code ?? ('TEN-' . $t->id) }}) — {{ $roomLabel }} [{{ $statusLabel }}]
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($groupedTenants['other']->isNotEmpty())
                            <optgroup label="Other Tenants (Unassigned / Inactive / Moved Out)">
                                @foreach($groupedTenants['other'] as $t)
                                    @php
                                        $roomLabel = $t->room ? 'Room ' . $t->room->room_number . ' (' . $t->room->room_type . ')' : 'No room assigned';
                                        $statusLabel = ucfirst(str_replace('_', ' ', $t->status));
                                    @endphp
                                    <option value="{{ $t->id }}" 
                                            data-room="{{ $roomLabel }}"
                                            data-rent="{{ $t->room ? $t->room->monthly_rent : 0 }}"
                                            data-has-room="{{ $t->room ? '1' : '0' }}"
                                            {{ old('tenant_id', $selectedTenant ? $selectedTenant->id : '') == $t->id ? 'selected' : '' }}>
                                        {{ $t->full_name }} ({{ $t->tenant_code ?? ('TEN-' . $t->id) }}) — {{ $roomLabel }} [{{ $statusLabel }}]
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    @else
                        @foreach($tenants as $t)
                            @php
                                $roomLabel = $t->room ? 'Room ' . $t->room->room_number . ' (' . $t->room->room_type . ')' : 'No room assigned';
                                $statusLabel = ucfirst(str_replace('_', ' ', $t->status));
                            @endphp
                            <option value="{{ $t->id }}" 
                                    data-room="{{ $roomLabel }}"
                                    data-rent="{{ $t->room ? $t->room->monthly_rent : 0 }}"
                                    data-has-room="{{ $t->room ? '1' : '0' }}"
                                    {{ old('tenant_id', $selectedTenant ? $selectedTenant->id : '') == $t->id ? 'selected' : '' }}>
                                {{ $t->full_name }} ({{ $t->tenant_code ?? ('TEN-' . $t->id) }}) — {{ $roomLabel }} [{{ $statusLabel }}]
                            </option>
                        @endforeach
                    @endif
                </select>
                <div class="form-text text-secondary" style="font-size: 0.76rem;">
                    All tenants (Active, Pending, Inactive, Moved Out) are listed for payment recording.
                </div>
                @error('tenant_id')
                    <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <!-- Room Display (Auto filled) -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Assigned Room & Rate</label>
                <input type="text" id="roomDisplay" class="form-control" readonly 
                       value="{{ $selectedTenant && $selectedTenant->room ? 'Room ' . $selectedTenant->room->room_number . ' (' . $selectedTenant->room->room_type . ') — ₱' . number_format($selectedTenant->room->monthly_rent, 2) . '/mo' : ($selectedTenant ? 'No room assigned' : 'Select a tenant above') }}">
                <div id="roomWarning" class="text-danger small mt-1 {{ ($selectedTenant && !$selectedTenant->room) ? '' : 'd-none' }}">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Tenant has no assigned room. Please assign a room first in Tenant Edit.
                </div>
            </div>

            <!-- Billing Month -->
            <div class="col-md-6">
                <label for="billing_month" class="form-label fw-semibold">Billing Month <span class="text-danger">*</span></label>
                <select name="billing_month" id="billing_month" class="form-select @error('billing_month') is-invalid @enderror" required>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ old('billing_month', $currentMonth) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(2000, $m, 1)->format('F') }}
                        </option>
                    @endfor
                </select>
                @error('billing_month')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Billing Year -->
            <div class="col-md-6">
                <label for="billing_year" class="form-label fw-semibold">Billing Year <span class="text-danger">*</span></label>
                <select name="billing_year" id="billing_year" class="form-select @error('billing_year') is-invalid @enderror" required>
                    @for($y = 2024; $y <= 2050; $y++)
                        <option value="{{ $y }}" {{ old('billing_year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                @error('billing_year')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Amount Paid -->
            <div class="col-md-4">
                <label for="amount" class="form-label fw-semibold">Amount Paid (₱) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">₱</span>
                    <input type="number" step="0.01" name="amount" id="amount" 
                           class="form-control border-start-0 ps-0 @error('amount') is-invalid @enderror" 
                           value="{{ old('amount', $selectedTenant && $selectedTenant->room ? $selectedTenant->room->monthly_rent : '') }}" 
                           required placeholder="Enter amount">
                </div>
                @error('amount')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Payment Date -->
            <div class="col-md-4">
                <label for="payment_date" class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                <input type="date" name="payment_date" id="payment_date" 
                       class="form-control @error('payment_date') is-invalid @enderror" 
                       value="{{ old('payment_date', date('Y-m-d')) }}" required>
                @error('payment_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Payment Time -->
            <div class="col-md-4">
                <label for="payment_time" class="form-label fw-semibold">Payment Time</label>
                <input type="time" name="payment_time" id="payment_time" 
                       class="form-control @error('payment_time') is-invalid @enderror" 
                       value="{{ old('payment_time', date('H:i')) }}">
                @error('payment_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Payment Method (CASH or GCASH only) -->
            <div class="col-md-6">
                <label for="payment_method" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required onchange="toggleReferenceField(this)">
                    <option value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="gcash" {{ old('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                </select>
                @error('payment_method')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Reference Number (GCash / Cash) -->
            <div class="col-md-6" id="refNumberGroup">
                <label for="gcash_reference" id="refNumberLabel" class="form-label fw-semibold">
                    Reference Number <span class="badge bg-secondary ms-1">CASH</span> <span class="text-muted">(Optional)</span>
                </label>
                <input type="text" name="gcash_reference" id="gcash_reference" 
                       class="form-control @error('gcash_reference') is-invalid @enderror" 
                       value="{{ old('gcash_reference') }}" placeholder="Enter reference/receipt number (optional)">
                @error('gcash_reference')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Receipt Upload -->
            <div class="col-md-12">
                <label for="receipt" class="form-label fw-semibold">Payment Proof (Receipt / Voucher / Screenshot)</label>
                <input type="file" name="receipt" id="receipt" 
                       class="form-control @error('receipt') is-invalid @enderror" 
                       accept="image/jpeg,image/png,image/webp,application/pdf">
                <div class="form-text" style="font-size: 0.78rem; color: var(--text-secondary);">
                    Allowed formats: JPG, PNG, WEBP, PDF (Max 5MB). File upload is optional for Cash payments.
                </div>
                @error('receipt')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remarks -->
            <div class="col-md-12">
                <label for="remarks" class="form-label fw-semibold">Remarks (Optional)</label>
                <textarea name="remarks" id="remarks" rows="2" 
                          class="form-control @error('remarks') is-invalid @enderror" 
                          placeholder="e.g. Paid in full for month of November">{{ old('remarks') }}</textarea>
                @error('remarks')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
            <a href="{{ route('admin.payments.index') }}" class="btn-secondary-custom text-decoration-none">Cancel</a>
            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-save"></i> Save Payment
            </button>
        </div>
    </form>
</div>

<script>
    function onTenantChanged(selectElem) {
        const selected = selectElem.options[selectElem.selectedIndex];
        if (!selected || !selected.value) {
            document.getElementById('roomDisplay').value = 'Select a tenant above';
            document.getElementById('roomWarning').classList.add('d-none');
            return;
        }

        const roomName = selected.getAttribute('data-room') || 'No room assigned';
        const rent = parseFloat(selected.getAttribute('data-rent') || '0');
        const hasRoom = selected.getAttribute('data-has-room') === '1';

        if (hasRoom && rent > 0) {
            document.getElementById('roomDisplay').value = roomName + ' — ₱' + rent.toLocaleString('en-US', { minimumFractionDigits: 2 }) + '/mo';
            document.getElementById('roomWarning').classList.add('d-none');
            // Auto fill amount if currently empty or 0
            const amountInput = document.getElementById('amount');
            if (!amountInput.value || parseFloat(amountInput.value) === 0) {
                amountInput.value = rent;
            }
        } else {
            document.getElementById('roomDisplay').value = roomName;
            document.getElementById('roomWarning').classList.remove('d-none');
        }
    }

    function toggleReferenceField(selectElem) {
        const isGcash = selectElem.value === 'gcash';
        const refInput = document.getElementById('gcash_reference');
        const label = document.getElementById('refNumberLabel');

        if (label) {
            label.innerHTML = isGcash 
                ? 'Reference Number <span class="badge bg-primary ms-1">GCASH</span> <span class="text-danger">*</span>' 
                : 'Reference Number <span class="badge bg-secondary ms-1">CASH</span> <span class="text-muted">(Optional)</span>';
        }
        if (refInput) {
            refInput.placeholder = isGcash 
                ? 'Enter GCash reference number (e.g. 100234891023)' 
                : 'Enter reference/receipt number (optional)';
            refInput.required = isGcash;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const paymentMethodSelect = document.getElementById('payment_method');
        if (paymentMethodSelect) {
            toggleReferenceField(paymentMethodSelect);
        }
    });
</script>
@endsection
