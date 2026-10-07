@extends('layouts.admin')

@section('title', 'Edit Payment: ' . $payment->payment_code)
@section('page_title', 'Edit Payment')
@section('page_subtitle', 'Payments > Payment List > Edit Payment')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Payment Details
    </a>

    @if($payment->is_edited)
        <a href="{{ route('admin.payments.history', $payment->id) }}" class="btn btn-outline-info btn-sm">
            <i class="bi bi-clock-history me-1"></i> View Existing Edit History ({{ $payment->editHistories->count() }})
        </a>
    @endif
</div>

<!-- Important Audit Trail Notice -->
<div class="alert alert-warning border-0 shadow-xs mb-4 p-3" style="border-radius: 10px;">
    <div class="d-flex align-items-start gap-3">
        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center p-2 flex-shrink-0" style="width: 38px; height: 38px;">
            <i class="bi bi-shield-exclamation fs-5"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1 text-dark">Official Audit Trail & Edit Indicator Policy</h6>
            <p class="mb-0 small text-dark" style="opacity: 0.9;">
                Saving changes to this payment will automatically mark it with an <strong>EDITED</strong> indicator badge. A new permanent audit record will be created capturing who edited it, the date/time, the mandatory reason, and exact Before & After changes. <strong>Edit history entries can never be deleted or overwritten.</strong>
            </p>
        </div>
    </div>
</div>

<div class="custom-card">
    <div class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center" style="border-color: var(--border-color) !important;">
        <div>
            <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Edit Payment Record {{ $payment->payment_code }}</h5>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">Update payment amount, rental period, date, status, or transaction proof.</p>
        </div>
        @if($payment->is_edited)
            <span class="badge bg-purple-subtle text-purple border border-purple px-3 py-2 fw-bold" style="font-size: 0.8rem;">
                <i class="bi bi-clock-history me-1"></i> CURRENTLY EDITED
            </span>
        @endif
    </div>

    <form action="{{ route('admin.payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data" id="editPaymentForm">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Tenant (Display) -->
            <div class="col-md-6">
                <label class="form-label">Tenant</label>
                <input type="text" class="form-control" readonly value="{{ $payment->tenant->full_name }} ({{ $payment->tenant->tenant_code }})">
            </div>

            <!-- Room (Display) -->
            <div class="col-md-6">
                <label class="form-label">Room</label>
                <input type="text" class="form-control" readonly value="{{ $payment->room ? 'Room ' . $payment->room->room_number . ' (' . $payment->room->room_type . ')' : 'Unassigned' }}">
            </div>

            <!-- Amount Paid -->
            <div class="col-md-4">
                <label for="amount" class="form-label">Amount Paid (₱) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">₱</span>
                    <input type="number" step="0.01" name="amount" id="amount" 
                           class="form-control border-start-0 ps-0 @error('amount') is-invalid @enderror" 
                           value="{{ old('amount', $payment->amount) }}" required>
                </div>
                @error('amount')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Payment Date -->
            <div class="col-md-4">
                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                <input type="date" name="payment_date" id="payment_date" 
                       class="form-control @error('payment_date') is-invalid @enderror" 
                       value="{{ old('payment_date', $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '') }}" required>
                @error('payment_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Payment Time -->
            <div class="col-md-4">
                <label for="payment_time" class="form-label">Payment Time</label>
                <input type="time" name="payment_time" id="payment_time" 
                       class="form-control @error('payment_time') is-invalid @enderror" 
                       value="{{ old('payment_time', $payment->payment_time ? \Carbon\Carbon::parse($payment->payment_time)->format('H:i') : ($payment->created_at ? $payment->created_at->format('H:i') : date('H:i'))) }}">
                @error('payment_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Rental Period: Month -->
            <div class="col-md-3">
                <label for="billing_month" class="form-label">Rental Month <span class="text-danger">*</span></label>
                <select name="billing_month" id="billing_month" class="form-select @error('billing_month') is-invalid @enderror" required>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ old('billing_month', $payment->billing_month) == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
                @error('billing_month')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Rental Period: Year -->
            <div class="col-md-3">
                <label for="billing_year" class="form-label">Rental Year <span class="text-danger">*</span></label>
                <select name="billing_year" id="billing_year" class="form-select @error('billing_year') is-invalid @enderror" required>
                    @for($y = 2020; $y <= 2050; $y++)
                        <option value="{{ $y }}" {{ old('billing_year', $payment->billing_year) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
                @error('billing_year')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Payment Method -->
            <div class="col-md-3">
                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                    <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="gcash" {{ old('payment_method', $payment->payment_method) == 'gcash' ? 'selected' : '' }}>GCash</option>
                </select>
                @error('payment_method')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Status -->
            <div class="col-md-3">
                <label for="status" class="form-label">Payment Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="paid" {{ old('status', $payment->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="verified" {{ old('status', $payment->status) == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>Pending Verification</option>
                    <option value="partial" {{ old('status', $payment->status) == 'partial' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="rejected" {{ old('status', $payment->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Reference Number -->
            <div class="col-md-6">
                <label for="gcash_reference" class="form-label">Reference Number (GCash)</label>
                <input type="text" name="gcash_reference" id="gcash_reference" 
                       class="form-control @error('gcash_reference') is-invalid @enderror" 
                       value="{{ old('gcash_reference', $payment->gcash_reference) }}" placeholder="GCash Reference No.">
                @error('gcash_reference')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Receipt File -->
            <div class="col-md-6">
                <label for="receipt" class="form-label">Update Payment Proof (Optional)</label>
                <input type="file" name="receipt" id="receipt" 
                       class="form-control @error('receipt') is-invalid @enderror" 
                       accept="image/jpeg,image/png,image/webp,application/pdf">
                @if($payment->receipt_path)
                    <div class="mt-1" style="font-size: 0.78rem;">
                        Current: <a href="{{ $payment->receipt_url }}" target="_blank" class="fw-semibold">View uploaded receipt</a>
                    </div>
                @endif
                @error('receipt')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remarks -->
            <div class="col-md-12">
                <label for="remarks" class="form-label">General Remarks (Optional)</label>
                <textarea name="remarks" id="remarks" rows="2" 
                          class="form-control @error('remarks') is-invalid @enderror" placeholder="Optional notes regarding this payment record...">{{ old('remarks', $payment->remarks) }}</textarea>
                @error('remarks')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- MANDATORY EDIT REASON SECTION -->
            <div class="col-12 mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
                <div class="p-3 rounded border" style="background-color: var(--table-header-bg); border-color: rgba(245, 158, 11, 0.4) !important;">
                    <label for="edit_reason" class="form-label fw-bold d-flex justify-content-between align-items-center mb-1">
                        <span>
                            <i class="bi bi-journal-text text-warning me-1"></i> Reason for Edit <span class="text-danger">*</span>
                        </span>
                        <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.72rem;">MANDATORY AUDIT FIELD</span>
                    </label>
                    <p class="text-secondary small mb-2">
                        State clearly why this payment is being altered. This explanation will be permanently recorded in the edit history.
                    </p>

                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 reason-chip" onclick="setEditReason('Incorrect amount entered')">
                            Incorrect amount entered
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 reason-chip" onclick="setEditReason('Wrong rental period')">
                            Wrong rental period
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 reason-chip" onclick="setEditReason('Wrong payment method')">
                            Wrong payment method
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 reason-chip" onclick="setEditReason('Corrected encoding error')">
                            Corrected encoding error
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 reason-chip" onclick="setEditReason('Tenant balance adjustment')">
                            Tenant balance adjustment
                        </button>
                    </div>

                    <textarea name="edit_reason" id="edit_reason" rows="2" 
                              class="form-control @error('edit_reason') is-invalid @enderror" 
                              placeholder="e.g. Incorrect amount entered; corrected to reflect actual cash payment received..." required>{{ old('edit_reason') }}</textarea>
                    @error('edit_reason')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn-secondary-custom text-decoration-none">Cancel</a>
            <button type="submit" class="btn-primary-custom px-4">
                <i class="bi bi-check-lg me-1"></i> Save Changes & Record Edit
            </button>
        </div>
    </form>
</div>

<script>
function setEditReason(text) {
    const input = document.getElementById('edit_reason');
    if (input) {
        input.value = text;
        input.focus();
    }
}
</script>
@endsection
