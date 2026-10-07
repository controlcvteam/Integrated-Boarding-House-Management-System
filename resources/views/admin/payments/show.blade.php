@extends('layouts.admin')

@section('title', 'Payment ' . $payment->payment_code)
@section('page_title', 'Payment Details')
@section('page_subtitle', 'Payments > Payment List > Payment Details')

@push('styles')
<style>
@media print {
    .receipt-screen-toolbar,
    .no-print,
    .d-print-none,
    .app-sidebar,
    .app-navbar,
    .admin-sidebar,
    button,
    .btn,
    [class*="btn-"],
    a[href*="payments"],
    .breadcrumb-wrapper {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        position: absolute !important;
        left: -9999px !important;
    }

    body, .app-main, .app-content, .app-wrapper {
        background: #ffffff !important;
        color: #0f172a !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .receipt-printable-card {
        border: 2px solid #1e293b !important;
        border-radius: 8px !important;
        padding: 2.5rem !important;
        box-shadow: none !important;
        max-width: 100% !important;
        margin: 0 auto !important;
        background: #ffffff !important;
        color: #0f172a !important;
    }
}
</style>
@endpush

@section('content')
<div class="receipt-screen-toolbar d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2 d-print-none no-print">
    <a href="{{ route('admin.payments.index') }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Payment List
    </a>

    <div class="d-flex gap-2">
        <button type="button" class="btn-secondary-custom py-1 px-3" style="font-size: 0.85rem;" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Receipt
        </button>

        @if($payment->status === 'pending')
            <button type="button" class="btn-outline-danger-custom py-1 px-3" style="font-size: 0.85rem;" 
                    data-bs-toggle="modal" data-bs-target="#rejectGcashModal">
                <i class="bi bi-x-circle"></i> Reject Payment
            </button>
            <form action="{{ route('admin.payments.approve-gcash', $payment->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-success-custom py-1 px-3" style="font-size: 0.85rem;">
                    <i class="bi bi-check-circle"></i> Approve Payment
                </button>
            </form>
        @else
            <a href="{{ route('admin.payments.edit', $payment->id) }}" class="btn-primary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
                <i class="bi bi-pencil"></i> Edit Payment
            </a>
        @endif
    </div>
</div>

<div class="row g-4 d-print-none">
    <!-- Card 1: Payment Information -->
    <div class="col-lg-4">
        <div class="custom-card h-100">
            <h6 class="fw-bold mb-3 border-bottom pb-2" style="font-size: 0.95rem; border-color: var(--border-color) !important;">
                Payment Information
            </h6>

            <div class="row g-2" style="font-size: 0.88rem;">
                <div class="col-5 text-secondary">Payment ID</div>
                <div class="col-7 fw-semibold">{{ $payment->payment_code ?? ('PAY-' . $payment->id) }}</div>

                <div class="col-5 text-secondary">Amount Paid</div>
                <div class="col-7 fw-bold text-success fs-6">₱{{ number_format($payment->amount, 2) }}</div>

                <div class="col-5 text-secondary">Payment Method</div>
                <div class="col-7">
                    <span class="badge {{ $payment->payment_method === 'cash' ? 'bg-secondary' : 'bg-primary' }}">
                        {{ strtoupper($payment->payment_method) }}
                    </span>
                </div>

                <div class="col-5 text-secondary">Payment Date</div>
                <div class="col-7 fw-semibold">{{ $payment->payment_date ? $payment->payment_date->format('F d, Y') : '-' }}</div>

                <div class="col-5 text-secondary">Payment Time</div>
                <div class="col-7 fw-semibold"><i class="bi bi-clock me-1 text-secondary"></i>{{ $payment->formatted_payment_time }}</div>

                <div class="col-5 text-secondary">Billing Period</div>
                <div class="col-7 fw-semibold">{{ $payment->billing_period_label }}</div>

                <div class="col-5 text-secondary">Reference No.</div>
                <div class="col-7 fw-semibold">{{ $payment->gcash_reference ?: 'None (Cash)' }}</div>

                <div class="col-5 text-secondary">Remarks</div>
                <div class="col-7">{{ $payment->remarks ?: 'None' }}</div>

                <div class="col-5 text-secondary">Payment Proof</div>
                <div class="col-7">
                    @if($payment->receipt_path)
                        <a href="{{ $payment->receipt_url }}" target="_blank" class="fw-bold text-decoration-none" style="color: #38bdf8;">
                            <i class="bi bi-file-earmark-image"></i> View Receipt
                        </a>
                    @else
                        <span class="text-muted">No attachment</span>
                    @endif
                </div>

                @if($payment->rejection_reason)
                    <div class="col-12 mt-2">
                        <div class="alert alert-danger py-2 px-3 mb-0" style="font-size: 0.82rem;">
                            <strong>Rejection Reason:</strong> {{ $payment->rejection_reason }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Card 2: Tenant Information -->
    <div class="col-lg-4">
        <div class="custom-card h-100">
            <h6 class="fw-bold mb-3 border-bottom pb-2" style="font-size: 0.95rem; border-color: var(--border-color) !important;">
                Tenant Information
            </h6>

            <div class="row g-2" style="font-size: 0.88rem;">
                <div class="col-5 text-secondary">Tenant ID</div>
                <div class="col-7 fw-semibold">{{ $payment->tenant->tenant_code ?? '-' }}</div>

                <div class="col-5 text-secondary">Tenant Name</div>
                <div class="col-7 fw-bold">
                    @if($payment->tenant_id && $payment->tenant)
                        <a href="{{ route('admin.tenants.show', $payment->tenant_id) }}" class="text-decoration-none" style="color: var(--text-primary);">
                            {{ $payment->tenant->full_name }}
                        </a>
                    @else
                        <span class="text-muted">{{ $payment->tenant->full_name ?? 'Tenant record removed' }}</span>
                    @endif
                </div>

                <div class="col-5 text-secondary">Room</div>
                <div class="col-7 fw-semibold">
                    @if($payment->room)
                        Room {{ $payment->room->room_number }} ({{ $payment->room->room_type }})
                    @else
                        -
                    @endif
                </div>

                <div class="col-5 text-secondary">Contact Number</div>
                <div class="col-7">{{ $payment->tenant->contact_number }}</div>

                <div class="col-5 text-secondary">Email Address</div>
                <div class="col-7 text-truncate">{{ $payment->tenant->user->email ?? '-' }}</div>

                <div class="col-5 text-secondary">Move-in Date</div>
                <div class="col-7">{{ $payment->tenant->move_in_date ? $payment->tenant->move_in_date->format('F d, Y') : '-' }}</div>

                <div class="col-5 text-secondary">Monthly Rent</div>
                <div class="col-7 fw-semibold">₱{{ number_format($payment->room ? $payment->room->monthly_rent : 0, 2) }}</div>

                <div class="col-5 text-secondary">Tenant Status</div>
                <div class="col-7">
                    <span class="badge-pill {{ $payment->tenant->status === 'active' ? 'badge-success' : 'badge-secondary' }}" style="font-size: 0.72rem;">
                        {{ ucfirst($payment->tenant->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Payment Summary -->
    <div class="col-lg-4">
        <div class="custom-card h-100">
            <h6 class="fw-bold mb-3 border-bottom pb-2" style="font-size: 0.95rem; border-color: var(--border-color) !important;">
                Payment Summary
            </h6>

            @php
                $monthlyRent = $payment->room ? $payment->room->monthly_rent : 0;
                $remainingBalance = max(0, $monthlyRent - $payment->amount);
            @endphp

            <div class="p-3 rounded mb-3" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.9rem;">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Monthly Rent (₱):</span>
                    <strong class="fs-6">₱{{ number_format($monthlyRent, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Amount Paid (₱):</span>
                    <strong class="text-success fs-6">₱{{ number_format($payment->amount, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Remaining Balance (₱):</span>
                    <strong class="{{ $remainingBalance > 0 ? 'text-danger' : 'text-success' }} fs-6">
                        ₱{{ number_format($remainingBalance, 2) }}
                    </strong>
                </div>
                <hr style="border-color: var(--border-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary">Status:</span>
                    <div class="d-flex align-items-center gap-1">
                        @if(in_array($payment->status, ['paid', 'verified']))
                            <span class="badge-pill badge-success"><i class="bi bi-check-circle-fill"></i> Paid</span>
                        @elseif($payment->status === 'partial')
                            <span class="badge-pill badge-info"><i class="bi bi-pie-chart-fill"></i> Partially Paid</span>
                        @elseif($payment->status === 'pending')
                            <span class="badge-pill badge-warning"><i class="bi bi-hourglass-split"></i> Pending Verification</span>
                        @else
                            <span class="badge-pill badge-danger"><i class="bi bi-x-circle-fill"></i> Rejected</span>
                        @endif

                        @if($payment->is_edited)
                            <a href="{{ route('admin.payments.history', $payment->id) }}" class="badge-edited ms-1 text-decoration-none" title="Click to view complete audit & edit history">
                                <i class="bi bi-clock-history"></i> EDITED
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes / Verification Record -->
            <div class="p-3 rounded" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.8rem;">
                <div class="fw-semibold mb-1 text-secondary">AUDIT TRAIL</div>
                <div>Recorded By: {{ $payment->receiver->name ?? 'System' }}</div>
                @if($payment->verified_by)
                    <div>Verified By: {{ $payment->verifier->name ?? 'Admin' }} ({{ $payment->verified_at ? $payment->verified_at->format('M d, Y h:i A') : '-' }})</div>
                @endif
                @if($payment->is_edited)
                    <div class="mt-2 pt-2 border-top text-purple fw-semibold" style="border-color: var(--border-color) !important;">
                        <i class="bi bi-pencil-square me-1"></i> Modified {{ $payment->editHistories->count() }} time(s). 
                        <a href="{{ route('admin.payments.history', $payment->id) }}" class="text-purple text-decoration-underline ms-1">View edits</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Complete Revision Audit Trail if Payment Has Been Edited -->
@if($payment->is_edited && $payment->editHistories->count() > 0)
    <div class="custom-card mt-4 d-print-none">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2" style="border-color: var(--border-color) !important;">
            <div>
                <h6 class="fw-bold mb-0 text-purple" style="font-size: 1rem;">
                    <i class="bi bi-journal-check me-2"></i> Payment Revision History ({{ $payment->editHistories->count() }} Revisions)
                </h6>
                <p class="text-secondary small mb-0">Complete audit trail of all adjustments made to this payment record.</p>
            </div>
            <a href="{{ route('admin.payments.history', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                View Full History Page &rarr;
            </a>
        </div>

        <div class="row g-3">
            @foreach($payment->editHistories as $idx => $history)
                @php
                    $revNumber = $payment->editHistories->count() - $idx;
                @endphp
                <div class="col-12">
                    <div class="audit-entry-card is-edit mb-0">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2 pb-1 border-bottom" style="border-color: var(--border-color) !important;">
                            <div>
                                <span class="badge bg-purple text-white fw-bold me-2">Edit #{{ $revNumber }}</span>
                                <strong style="color: var(--text-primary);">{{ $history->created_at->format('F j, Y — g:i A') }}</strong>
                            </div>
                            <div class="small">
                                <span class="text-secondary">Edited by:</span>
                                <strong class="text-primary">{{ $history->editor_display_name }}</strong>
                            </div>
                        </div>

                        <div class="p-2 rounded mb-2 bg-light border small">
                            <span class="fw-bold text-warning text-uppercase" style="font-size: 0.72rem;">Reason:</span>
                            <span class="ms-1 fw-semibold text-dark">"{{ $history->reason }}"</span>
                        </div>

                        @if(!empty($history->changed_fields))
                            <div class="row g-2">
                                @foreach($history->changed_fields as $fKey => $c)
                                    <div class="col-md-6">
                                        <div class="before-after-box mb-0">
                                            <div>
                                                <div class="text-secondary small fw-semibold" style="font-size: 0.68rem;">
                                                    {{ $c['field_label'] ?? ucfirst($fKey) }} (BEFORE)
                                                </div>
                                                <div class="before-pill mt-1">{{ $c['old'] ?? '—' }}</div>
                                            </div>
                                            <div class="text-secondary px-1">&rarr;</div>
                                            <div>
                                                <div class="text-secondary small fw-semibold" style="font-size: 0.68rem;">
                                                    {{ $c['field_label'] ?? ucfirst($fKey) }} (AFTER)
                                                </div>
                                                <div class="after-pill mt-1">{{ $c['new'] ?? '—' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Preview of Receipt Image if available -->
@if($payment->receipt_path)
    <div class="custom-card mt-4 d-print-none">
        <h6 class="fw-bold mb-3" style="font-size: 0.95rem;">Payment Receipt Proof</h6>
        <div class="text-center p-3 rounded" style="background-color: var(--table-header-bg);">
            <img src="{{ $payment->receipt_url }}" alt="Receipt Proof" class="img-fluid rounded shadow-sm" style="max-height: 500px; object-fit: contain;">
        </div>
    </div>
@endif

<!-- Reject Payment Modal -->
<div class="modal fade" id="rejectGcashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-card p-4 border-0">
            <h5 class="fw-bold mb-2">Reject {{ strtoupper($payment->payment_method) }} Payment</h5>
            <p class="text-secondary mb-3" style="font-size: 0.88rem;">
                Please provide the reason why this {{ $payment->payment_method === 'cash' ? 'cash' : 'GCash' }} payment could not be accepted.
            </p>

            <form action="{{ route('admin.payments.reject-gcash', $payment->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" class="form-control" required 
                              placeholder="e.g. Reference number does not match receipt, or amount is incorrect."></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-danger-custom">Reject Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- PRINT ONLY: Clean, Official Payment Acknowledgment Receipt -->
<div class="d-none d-print-block receipt-printable-card position-relative overflow-hidden p-4 p-md-5">
    <!-- Watermark for Paid Status in Print -->
    @if(in_array($payment->status, ['paid', 'verified']))
        <div class="receipt-watermark d-none d-print-block position-absolute top-50 start-50 translate-middle text-uppercase fw-bolder text-success" 
             style="font-size: 5.5rem; transform: translate(-50%, -50%) rotate(-25deg); pointer-events: none; z-index: 0; letter-spacing: 0.25em; border: 8px solid rgba(16, 185, 129, 0.25); border-radius: 20px; padding: 1rem 3.5rem; opacity: 0.12;">
            PAID
        </div>
    @endif

    <div class="text-center pb-4 border-bottom position-relative z-1" style="border-color: #cbd5e1 !important;">
        <div class="d-inline-flex align-items-center justify-content-center gap-2 mb-2">
            <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center shadow-xs" style="width: 48px; height: 48px;">
                <i class="bi bi-house-door-fill fs-3"></i>
            </div>
            <div class="text-start">
                <h4 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px; font-size: 1.25rem;">Integrated Boarding House</h4>
                <div class="text-secondary small fw-medium">Official Property Rent Acknowledgment Receipt</div>
            </div>
        </div>
        <div class="d-flex justify-content-center align-items-center gap-2 mt-2 flex-wrap">
            <span class="badge bg-light text-secondary border px-3 py-1 font-monospace fw-bold" style="font-size: 0.85rem;">
                #REC-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}
            </span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 font-monospace fw-bold" style="font-size: 0.85rem;">
                {{ $payment->payment_code ?? ('PAY-' . $payment->id) }}
            </span>
        </div>
        <div class="text-muted small mt-2" style="font-size: 0.78rem;">
            Issued: {{ $payment->payment_date ? $payment->payment_date->format('F d, Y') : $payment->created_at->format('F d, Y') }} • {{ $payment->formatted_payment_time }}
        </div>
    </div>

    <!-- Receipt Meta Details -->
    <div class="row g-3 py-4 border-bottom position-relative z-1" style="border-color: #cbd5e1 !important;">
        <div class="col-6">
            <span class="text-secondary small d-block fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Tenant Information</span>
            <strong class="fs-5 text-primary d-block mt-1">{{ $payment->tenant->full_name ?? ($payment->tenant->user->name ?? 'Tenant') }}</strong>
            <div class="text-secondary small mt-1">Tenant ID: <strong>{{ $payment->tenant->tenant_code ?? '-' }}</strong></div>
            <div class="text-secondary small">Assigned Room: <strong>Room {{ $payment->room->room_number ?? 'N/A' }}</strong> ({{ $payment->room->room_type ?? 'Standard' }})</div>
            <div class="text-secondary small">Contact: {{ $payment->tenant->contact_number ?: 'N/A' }}</div>
        </div>
        <div class="col-6 text-end">
            <span class="text-secondary small d-block fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Payment & Period</span>
            <strong class="text-body fs-6 d-block mt-1">{{ $payment->payment_date ? $payment->payment_date->format('F d, Y') : $payment->created_at->format('F d, Y') }} <span class="small text-secondary fw-normal">at {{ $payment->formatted_payment_time }}</span></strong>
            <div class="text-secondary small mt-1">Billing Period: <strong>{{ $payment->billing_period_label }}</strong></div>
            <div class="mt-2">
                Status: 
                @if(in_array($payment->status, ['paid', 'verified']))
                    <span class="badge bg-success px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i> Fully Paid</span>
                @elseif($payment->status === 'partial')
                    <span class="badge bg-warning text-dark border border-warning px-3 py-1"><i class="bi bi-pie-chart-fill me-1"></i> Partially Paid</span>
                @elseif($payment->status === 'pending')
                    <span class="badge bg-info text-dark px-3 py-1"><i class="bi bi-hourglass-split me-1"></i> Waiting for Approval</span>
                @else
                    <span class="badge bg-danger px-3 py-1"><i class="bi bi-x-circle-fill me-1"></i> Rejected</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="py-4 border-bottom position-relative z-1" style="border-color: #cbd5e1 !important;">
        <table class="table table-borderless align-middle mb-0">
            <thead class="text-secondary small text-uppercase" style="border-bottom: 2px solid #334155; font-size: 0.75rem;">
                <tr>
                    <th>Item Description</th>
                    <th>Payment Method</th>
                    <th>Reference / Notes</th>
                    <th class="text-end">Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="fw-bold">Boarding House Monthly Rent</div>
                        <div class="small text-secondary">Period: {{ $payment->billing_period_label }} &bull; Room {{ $payment->room->room_number ?? 'N/A' }}</div>
                    </td>
                    <td><span class="badge bg-light text-dark border px-2 py-1 text-uppercase">{{ $payment->payment_method }}</span></td>
                    <td><span class="small font-monospace">{{ $payment->gcash_reference ?: 'Cash Payment' }}</span></td>
                    <td class="text-end fw-bold fs-5 text-success">₱{{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Total Paid -->
    <div class="p-3 my-3 bg-light rounded d-flex justify-content-between align-items-center position-relative z-1 border">
        <div>
            <span class="fw-bold text-uppercase small text-secondary d-block">Amount Received:</span>
            @if($payment->status === 'partial' && $remainingBalance > 0)
                <span class="small text-danger fw-semibold">Remaining Balance: ₱{{ number_format($remainingBalance, 2) }}</span>
            @elseif(in_array($payment->status, ['paid', 'verified']))
                <span class="small text-success fw-semibold"><i class="bi bi-check2-all me-1"></i> Balance: ₱0.00 (Fully Settled)</span>
            @endif
        </div>
        <div class="text-end">
            <span class="fs-3 fw-bold text-success">₱{{ number_format($payment->amount, 2) }}</span>
        </div>
    </div>

    <!-- Signatures -->
    <div class="row pt-5 mt-4 border-top text-center position-relative z-1" style="border-color: #cbd5e1 !important;">
        <div class="col-6">
            <div class="border-bottom pb-2 mb-2 mx-auto" style="max-width: 220px; border-color: #0f172a !important;">
                <span class="fw-bold">{{ $payment->tenant->full_name ?? ($payment->tenant->user->name ?? 'Tenant') }}</span>
            </div>
            <span class="small text-secondary">Tenant Signature</span>
        </div>
        <div class="col-6">
            <div class="border-bottom pb-2 mb-2 mx-auto" style="max-width: 220px; border-color: #0f172a !important;">
                <span class="fw-bold text-primary">ADMINISTRATION</span>
            </div>
            <span class="small text-secondary">Authorized Signature & Stamp</span>
        </div>
    </div>

    <div class="text-center text-secondary small mt-4 pt-3 border-top position-relative z-1" style="font-size: 0.75rem; border-color: #cbd5e1 !important;">
        Official computer-generated receipt issued by Integrated Boarding House Management System.
    </div>
</div>
@endsection
