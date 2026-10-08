@extends('layouts.tenant')

@section('title', !empty($isGcashOnly) ? 'Pay Now via GCash' : 'SUBMIT A PAYMENT')

@section('content')
<div class="mb-4">
    <a href="{{ route('tenant.payments.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Payments
    </a>
    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
        <h1 class="h3 fw-bold mb-0">{{ !empty($isGcashOnly) ? 'Pay Now via GCash' : 'SUBMIT A PAYMENT' }}</h1>
        @if(!empty($isGcashOnly))
            <span class="badge bg-primary px-3 py-1 font-monospace fw-semibold" style="font-size: 0.8rem;">
                <i class="bi bi-phone me-1"></i> GCash Only
            </span>
        @else
            <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 font-monospace fw-semibold" style="font-size: 0.8rem;">
                <i class="bi bi-wallet2 me-1"></i> Cash & GCash
            </span>
        @endif
    </div>
    <p class="text-muted mb-0">
        @if(!empty($isGcashOnly))
            Submit your online GCash rent transaction details and screenshot proof for prompt Landlord verification.
        @else
            Choose your preferred method: pay directly in Cash (no receipt upload required) or submit your GCash payment proof.
        @endif
    </p>
</div>

<div class="row g-4">
    <!-- Payment Form Column -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0">Rent Payment Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.payments.store') }}" method="POST" enctype="multipart/form-data" id="rentPaymentForm">
                    @csrf

                    <!-- Assigned Room Summary -->
                    <div class="p-3 bg-light rounded mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Assigned Accommodation:</span>
                            <h5 class="fw-bold text-primary mb-0">Room {{ $tenant->room->room_number }} ({{ $tenant->room->room_type }})</h5>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small">Standard Monthly Rent:</span>
                            <h5 class="fw-bold text-success mb-0">₱{{ number_format($monthlyRent, 2) }}</h5>
                        </div>
                    </div>

                    <!-- Payment Method Selector -->
                    @if(!empty($isGcashOnly))
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">
                                Selected Payment Method <span class="text-primary">(GCash Direct Online)</span>
                            </label>
                            <div class="card p-3 border method-select-card border-primary bg-primary-subtle" id="cardMethodGcash">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="radio" name="payment_method" value="gcash" class="form-check-input mt-0" checked readonly>
                                        <div>
                                            <div class="fw-bold text-primary"><i class="bi bi-phone me-1"></i> GCash Transfer</div>
                                            <small class="text-muted d-block">Scan QR code or transfer to GCash number, then upload screenshot proof.</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="bi bi-lock-fill me-1"></i> GCash Only
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
                                <span class="text-secondary small">
                                    <i class="bi bi-info-circle me-1"></i> Need to record a Cash payment instead?
                                </span>
                                <a href="{{ route('tenant.payments.submit') }}" class="btn btn-sm btn-link text-decoration-none py-0">
                                    Switch to "SUBMIT A PAYMENT" for Cash option &rarr;
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Select Payment Method <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="card p-3 border cursor-pointer h-100 method-select-card" id="cardMethodCash" style="transition: all 0.2s ease;">
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="radio" name="payment_method" value="cash" class="form-check-input mt-0" {{ old('payment_method') === 'cash' ? 'checked' : '' }} onchange="togglePaymentMethod('cash')">
                                            <div>
                                                <div class="fw-bold text-success"><i class="bi bi-cash-stack me-1"></i> Cash Payment</div>
                                                <small class="text-muted d-block">Pay in cash directly to Landlord. <strong>No proof needed</strong> (awaits Landlord confirmation).</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="card p-3 border cursor-pointer h-100 method-select-card" id="cardMethodGcash" style="transition: all 0.2s ease;">
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="radio" name="payment_method" value="gcash" class="form-check-input mt-0" {{ old('payment_method', 'gcash') === 'gcash' ? 'checked' : '' }} onchange="togglePaymentMethod('gcash')">
                                            <div>
                                                <div class="fw-bold text-primary"><i class="bi bi-phone me-1"></i> GCash Transfer</div>
                                                <small class="text-muted d-block">Scan QR code or send to GCash number, then upload screenshot proof.</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Cash Notice Banner (Shown when Cash is selected) -->
                    <div id="cashNoticeBox" class="alert alert-info border-0 shadow-xs mb-4 d-none">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-info-circle-fill fs-4 text-info flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold mb-1 text-info-emphasis">Cash Payment Policy</h6>
                                <p class="mb-0 small text-info-emphasis">
                                    No proof or receipt screenshot is required for cash payments. Once you submit this record, it will have a status of <strong>Pending Verification</strong> until the Landlord confirms receipt of the cash payment in person.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Billing Month -->
                        <div class="col-md-6">
                            <label for="billing_month" class="form-label fw-semibold">Billing Month <span class="text-danger">*</span></label>
                            <select name="billing_month" id="billing_month" class="form-select @error('billing_month') is-invalid @enderror" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ old('billing_month', $currentMonth) == $m ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
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
                                    <option value="{{ $y }}" {{ old('billing_year', $currentYear) == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                            @error('billing_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <!-- Amount -->
                        <div class="col-md-4">
                            <label for="amount" class="form-label fw-semibold">Amount to Pay (₱) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $monthlyRent) }}" required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Date -->
                        <div class="col-md-4">
                            <label for="payment_date" class="form-label fw-semibold">Date of Payment <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Time -->
                        <div class="col-md-4">
                            <label for="payment_time" class="form-label fw-semibold">Time of Payment</label>
                            <input type="time" name="payment_time" id="payment_time" class="form-control @error('payment_time') is-invalid @enderror" value="{{ old('payment_time', date('H:i')) }}">
                            @error('payment_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- GCash Reference Number (GCash Only) -->
                        <div class="col-12" id="gcashReferenceWrapper">
                            <label for="gcash_reference" class="form-label fw-semibold">GCash Reference Number <span class="text-danger" id="gcashRefRequiredStar">*</span></label>
                            <input type="text" name="gcash_reference" id="gcash_reference" class="form-control font-monospace @error('gcash_reference') is-invalid @enderror" placeholder="e.g. 1000 8829 4812" value="{{ old('gcash_reference') }}">
                            <div class="form-text small">Enter the reference number shown on your GCash transaction confirmation receipt.</div>
                            @error('gcash_reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Proof of Payment Screenshot (GCash Only) -->
                        <div class="col-12" id="gcashReceiptWrapper">
                            <label for="receipt" class="form-label fw-semibold">Upload GCash Screenshot / Receipt <span class="text-danger" id="gcashReceiptRequiredStar">*</span></label>
                            <input type="file" name="receipt" id="receipt" class="form-control @error('receipt') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewReceipt(this)">
                            <div class="form-text small">Accepted formats: JPG, JPEG, PNG, WEBP. Max file size: 5MB.</div>
                            @error('receipt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Image Preview Container -->
                            <div id="receipt-preview-container" class="mt-3 d-none">
                                <span class="small text-muted d-block mb-1">Receipt Preview:</span>
                                <img id="receipt-preview" src="#" alt="Screenshot preview" class="img-thumbnail" style="max-height: 250px;">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label fw-semibold">Tenant Remarks / Note (Optional)</label>
                            <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" placeholder="Any additional notes or instructions for the Landlord...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="{{ route('tenant.payments.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4" id="submitPaymentBtn">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Submit Payment Proof
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: GCash Info or Cash Guide -->
    <div class="col-lg-4">
        <!-- GCash Account Details Card -->
        <div id="gcashSidebarContainer">
            <!-- Admin GCash Account Card -->
            <div class="card border-0 shadow-sm text-white mb-4" style="background: linear-gradient(135deg, #007dfe 0%, #0056b3 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-white text-primary fw-bold">Official GCash Receiver</span>
                        <i class="bi bi-qr-code-scan fs-3"></i>
                    </div>
                    <h6 class="text-white-50 text-uppercase mb-1" style="letter-spacing: 1px;">GCash Account Name</h6>
                    <h4 class="fw-bold mb-3">{{ $gcashName }}</h4>

                    <div class="p-3 bg-white bg-opacity-10 rounded">
                        <span class="text-white-50 small d-block">GCash Mobile Number:</span>
                        <h3 class="fw-bold font-monospace tracking-wide mb-0">{{ $gcashNumber }}</h3>
                    </div>
                </div>
            </div>

            <!-- GCash QR Code Card -->
            <div class="card border-0 shadow-sm text-center mb-4">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-qr-code me-1"></i> Scan GCash QR Code
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="p-3 bg-light rounded d-inline-block border mb-3">
                        <img src="{{ asset('storage/' . ($gcashQrPath && $gcashQrPath !== '0' ? $gcashQrPath : 'settings/default-gcash-qr.svg')) }}?v=2" alt="GCash QR Code" class="img-fluid" style="max-height: 220px; max-width: 220px; object-fit: contain;" onerror="this.onerror=null; this.src='{{ asset('storage/settings/default-gcash-qr.svg') }}';">
                    </div>
                    <p class="small text-muted mb-3">Open your GCash app, tap <strong>QR</strong> at the bottom, and scan this code to pay.</p>
                    <a href="{{ asset('storage/' . ($gcashQrPath && $gcashQrPath !== '0' ? $gcashQrPath : 'settings/default-gcash-qr.svg')) }}?v=2" download="IBHMS-GCash-QR" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-download me-1"></i> Download QR Image
                    </a>
                </div>
            </div>

            <!-- How It Works Instructions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i> How to Pay via GCash</h6>
                </div>
                <div class="card-body">
                    <ol class="small text-muted ps-3 mb-0">
                        <li class="mb-2">Open your <strong>GCash App</strong> on your smartphone.</li>
                        <li class="mb-2">Tap <strong>Scan QR</strong> or send directly to <strong>{{ $gcashNumber }}</strong> ({{ $gcashName }}).</li>
                        <li class="mb-2">Enter your exact room rental amount.</li>
                        <li class="mb-2">Include a note: <em>"Rent [Room {{ $tenant->room->room_number }}] - {{ auth()->user()->name }}"</em>.</li>
                        <li class="mb-2">Capture a clear <strong>screenshot</strong> of the transaction showing the Reference Number.</li>
                        <li>Upload the screenshot in the form and submit.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Cash Payment Guide Card (Shown when Cash is selected) -->
        <div id="cashSidebarContainer" class="d-none">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="bi bi-cash-stack me-2"></i> Cash Payment Guidelines
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 bg-success-subtle rounded border border-success-subtle mb-3">
                        <span class="fw-bold text-success d-block mb-1">Direct Landlord Collection</span>
                        <p class="small text-success-emphasis mb-0">
                            Cash payments are verified in person by the Landlord. No proof upload is required on this website.
                        </p>
                    </div>

                    <h6 class="fw-semibold small text-uppercase text-muted mb-2">Step-by-Step:</h6>
                    <ol class="small text-muted ps-3 mb-3">
                        <li class="mb-2">Submit this form to create a pending payment record in the system.</li>
                        <li class="mb-2">Prepare the exact cash amount of <strong>₱{{ number_format($monthlyRent, 2) }}</strong>.</li>
                        <li class="mb-2">Hand the cash to the Landlord or boarding house administration desk.</li>
                        <li>The Landlord will inspect, count, and immediately verify your payment on the dashboard.</li>
                    </ol>

                    <div class="p-3 bg-light rounded small text-muted">
                        <i class="bi bi-shield-check text-primary me-1"></i> Once verified by the Landlord, your official print receipt will become available immediately.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePaymentMethod(method) {
    const isCash = (method === 'cash');
    const gcashRefWrapper = document.getElementById('gcashReferenceWrapper');
    const gcashReceiptWrapper = document.getElementById('gcashReceiptWrapper');
    const gcashRefInput = document.getElementById('gcash_reference');
    const gcashReceiptInput = document.getElementById('receipt');
    const gcashRefStar = document.getElementById('gcashRefRequiredStar');
    const gcashReceiptStar = document.getElementById('gcashReceiptRequiredStar');
    const cashNoticeBox = document.getElementById('cashNoticeBox');
    const gcashSidebar = document.getElementById('gcashSidebarContainer');
    const cashSidebar = document.getElementById('cashSidebarContainer');
    const submitBtn = document.getElementById('submitPaymentBtn');

    const cardGcash = document.getElementById('cardMethodGcash');
    const cardCash = document.getElementById('cardMethodCash');

    if (isCash) {
        // Cash mode: hide GCash requirements
        gcashRefWrapper.classList.add('d-none');
        gcashReceiptWrapper.classList.add('d-none');
        gcashRefInput.removeAttribute('required');
        gcashReceiptInput.removeAttribute('required');
        if (gcashRefStar) gcashRefStar.classList.add('d-none');
        if (gcashReceiptStar) gcashReceiptStar.classList.add('d-none');

        cashNoticeBox.classList.remove('d-none');
        gcashSidebar.classList.add('d-none');
        cashSidebar.classList.remove('d-none');

        submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Submit Cash Payment Notice';
        submitBtn.className = 'btn btn-success px-4';

        if (cardCash) cardCash.classList.add('border-success', 'bg-success-subtle');
        if (cardGcash) cardGcash.classList.remove('border-primary', 'bg-primary-subtle');
    } else {
        // GCash mode: show requirements
        gcashRefWrapper.classList.remove('d-none');
        gcashReceiptWrapper.classList.remove('d-none');
        gcashRefInput.setAttribute('required', 'required');
        gcashReceiptInput.setAttribute('required', 'required');
        if (gcashRefStar) gcashRefStar.classList.remove('d-none');
        if (gcashReceiptStar) gcashReceiptStar.classList.remove('d-none');

        cashNoticeBox.classList.add('d-none');
        gcashSidebar.classList.remove('d-none');
        cashSidebar.classList.add('d-none');

        submitBtn.innerHTML = '<i class="bi bi-cloud-arrow-up me-1"></i> Submit Payment Proof';
        submitBtn.className = 'btn btn-primary px-4';

        if (cardGcash) cardGcash.classList.add('border-primary', 'bg-primary-subtle');
        if (cardCash) cardCash.classList.remove('border-success', 'bg-success-subtle');
    }
}

function previewReceipt(input) {
    const previewContainer = document.getElementById('receipt-preview-container');
    const previewImg = document.getElementById('receipt-preview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.classList.add('d-none');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkedMethod = document.querySelector('input[name="payment_method"]:checked');
    if (checkedMethod) {
        togglePaymentMethod(checkedMethod.value);
    } else {
        togglePaymentMethod('gcash');
    }
});
</script>
@endsection
