@extends('layouts.tenant')

@section('title', 'Payment Receipt #REC-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT))

@push('styles')
<style>
@media print {
    .receipt-screen-toolbar,
    .no-print,
    .d-print-none,
    .app-sidebar,
    .app-navbar,
    .tenant-sidebar,
    .admin-sidebar,
    button,
    .btn,
    [class*="btn-"],
    a[href*="payments"] {
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
<!-- Screen-only action toolbar -->
<div class="receipt-screen-toolbar mb-4 d-print-none no-print">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <a href="{{ route('tenant.payments.index') }}" class="btn-secondary-custom text-decoration-none py-2 px-3 shadow-xs">
            <i class="bi bi-chevron-left"></i> Back to Payments
        </a>
        <div class="d-flex align-items-center gap-2">
            @if($payment->is_edited)
                <a href="{{ route('tenant.payments.history', $payment->id) }}" class="btn btn-outline-purple py-2 px-3 shadow-xs">
                    <i class="bi bi-clock-history me-1"></i> View Edit History
                </a>
            @endif
            <button type="button" onclick="window.print()" class="btn-primary-custom py-2 px-3 shadow-sm">
                <i class="bi bi-printer-fill"></i> Print Official Receipt
            </button>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card receipt-printable-card border-0 shadow-sm p-4 p-md-5 position-relative overflow-hidden" 
             style="background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color);">
            
            <!-- Watermark for Paid Status in Print -->
            @if(in_array($payment->status, ['paid', 'verified']))
                <div class="receipt-watermark d-none d-print-block position-absolute top-50 start-50 translate-middle text-uppercase fw-bolder text-success" 
                     style="font-size: 5.5rem; transform: translate(-50%, -50%) rotate(-25deg); pointer-events: none; z-index: 0; letter-spacing: 0.25em; border: 8px solid rgba(16, 185, 129, 0.25); border-radius: 20px; padding: 1rem 3.5rem; opacity: 0.12;">
                    PAID
                </div>
            @endif

            <!-- Receipt Official Header -->
            <div class="text-center pb-4 border-bottom position-relative z-1" style="border-color: var(--border-color) !important;">
                <div class="d-inline-flex align-items-center justify-content-center gap-2 mb-2">
                    <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center shadow-xs" style="width: 48px; height: 48px;">
                        <i class="bi bi-house-door-fill fs-3"></i>
                    </div>
                    <div class="text-start">
                        <h4 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px; font-size: 1.25rem; color: var(--text-primary);">Integrated Boarding House</h4>
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
                    @if($payment->is_edited)
                        <span class="badge bg-purple text-white px-3 py-1 font-monospace fw-bold" style="font-size: 0.85rem;">
                            <i class="bi bi-clock-history me-1"></i> EDITED
                        </span>
                    @endif
                </div>
                <div class="text-muted small mt-2" style="font-size: 0.78rem;">
                    Issued: {{ $payment->payment_date ? $payment->payment_date->format('F d, Y') : $payment->created_at->format('F d, Y') }} • {{ $payment->formatted_payment_time }}
                </div>
            </div>

            <!-- Receipt Meta Details -->
            <div class="row g-3 py-4 border-bottom position-relative z-1" style="border-color: var(--border-color) !important;">
                <div class="col-sm-6">
                    <span class="text-secondary small d-block fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Tenant Information</span>
                    <strong class="fs-5 text-primary d-block mt-1">{{ $payment->tenant->full_name ?? ($payment->tenant->user->name ?? 'Tenant') }}</strong>
                    <div class="text-secondary small mt-1"><i class="bi bi-person-badge me-1"></i> Tenant ID: <span class="fw-semibold text-body">{{ $payment->tenant->tenant_code }}</span></div>
                    <div class="text-secondary small"><i class="bi bi-door-open me-1"></i> Assigned Room: <strong class="text-body">Room {{ $payment->room->room_number ?? 'N/A' }}</strong> ({{ $payment->room->room_type ?? 'Standard' }})</div>
                    <div class="text-secondary small"><i class="bi bi-telephone me-1"></i> Contact: <span class="text-body">{{ $payment->tenant->contact_number ?: 'N/A' }}</span></div>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <span class="text-secondary small d-block fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Payment & Billing Period</span>
                    <strong class="text-body fs-6 d-block mt-1">
                        <i class="bi bi-calendar-event me-1"></i> {{ $payment->payment_date ? $payment->payment_date->format('F d, Y') : $payment->created_at->format('F d, Y') }}
                        <span class="small text-secondary fw-normal ms-1"><i class="bi bi-clock me-1"></i>{{ $payment->formatted_payment_time }}</span>
                    </strong>
                    <div class="text-secondary small mt-1">Billing Month: <strong class="text-body">{{ $payment->billing_period }}</strong></div>
                    <div class="mt-2 d-flex align-items-center justify-content-sm-end gap-1 flex-wrap">
                        <span class="text-secondary small">Status:</span>
                        @if(in_array($payment->status, ['paid', 'verified']))
                            <span class="badge bg-success px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i> Fully Paid</span>
                        @elseif($payment->status === 'partial')
                            <span class="badge bg-warning text-dark border border-warning px-3 py-1"><i class="bi bi-pie-chart-fill me-1"></i> Partially Paid</span>
                        @elseif($payment->status === 'pending')
                            <span class="badge bg-info text-dark px-3 py-1"><i class="bi bi-hourglass-split me-1"></i> Waiting for Approval</span>
                        @else
                            <span class="badge bg-danger px-3 py-1"><i class="bi bi-x-circle-fill me-1"></i> Rejected</span>
                        @endif

                        @if($payment->is_edited)
                            <a href="{{ route('tenant.payments.history', $payment->id) }}" class="badge-edited text-decoration-none" title="Payment record adjusted by Landlord. Click to view edit history">
                                <i class="bi bi-clock-history"></i> EDITED
                            </a>
                        @endif
                    </div>
                </div>
            </div>


            <!-- Receipt Breakdown Table -->
            <div class="py-4 border-bottom position-relative z-1" style="border-color: var(--border-color) !important;">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="text-secondary small text-uppercase" style="border-bottom: 2px solid var(--border-color); font-size: 0.75rem;">
                            <tr>
                                <th>Item Description</th>
                                <th>Payment Method</th>
                                <th>Transaction / Ref #</th>
                                <th class="text-end">Amount Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-bold text-body">Boarding House Monthly Rent</div>
                                    <div class="text-secondary small">Cycle: {{ $payment->billing_period }} &bull; Room {{ $payment->room->room_number ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $payment->payment_method === 'cash' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-2 py-1 text-uppercase fw-bold">
                                        {{ $payment->payment_method }}
                                    </span>
                                </td>
                                <td>
                                    <span class="font-monospace small text-body fw-medium">{{ $payment->gcash_reference ?: 'Direct Cash Receipt' }}</span>
                                </td>
                                <td class="text-end fw-bold fs-5 text-success">
                                    ₱{{ number_format($payment->amount, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total Amount Summary Card -->
            <div class="p-3 my-3 rounded-3 d-flex justify-content-between align-items-center position-relative z-1" 
                 style="background: var(--table-header-bg); border: 1px solid var(--border-color);">
                <div>
                    <span class="fw-bold text-uppercase small text-secondary d-block">Total Amount Credited:</span>
                    @if($payment->status === 'partial' && $payment->room)
                        @php $bal = max(0, (float)$payment->room->monthly_rent - (float)$payment->amount); @endphp
                        <span class="small text-danger fw-semibold">Remaining Balance: ₱{{ number_format($bal, 2) }}</span>
                    @elseif(in_array($payment->status, ['paid', 'verified']))
                        <span class="small text-success fw-semibold"><i class="bi bi-check2-all me-1"></i> Balance: ₱0.00 (Fully Settled)</span>
                    @endif
                </div>
                <div class="text-end">
                    <span class="fs-3 fw-bold text-success">₱{{ number_format($payment->amount, 2) }}</span>
                </div>
            </div>

            <!-- Administrative Correction Notice if edited -->
            @if($payment->is_edited)
            <div class="mb-4 p-3 rounded position-relative z-1" style="background: rgba(124, 58, 237, 0.06); border: 1px solid rgba(124, 58, 237, 0.25);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <span class="fw-bold text-uppercase small d-block" style="color: #7c3aed; font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="bi bi-shield-check me-1"></i> Official Administrative Correction Notice
                        </span>
                        <div class="text-secondary small mt-1">
                            This payment was officially adjusted by the Property Administrator/Landlord.
                            @if($payment->editHistories->first())
                                <span class="d-block text-body mt-1">Latest Reason: "<em>{{ $payment->editHistories->first()->reason }}</em>" ({{ $payment->editHistories->first()->created_at->format('M d, Y') }})</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-print-none">
                        <a href="{{ route('tenant.payments.history', $payment->id) }}" class="btn btn-sm btn-outline-purple py-1 px-3" style="font-size: 0.78rem;">
                            <i class="bi bi-clock-history me-1"></i> View Revision Log ({{ $payment->editHistories->count() }})
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes & Remarks -->
            @if($payment->notes || $payment->remarks)
            <div class="mb-4 position-relative z-1">
                <span class="small text-secondary d-block fw-semibold text-uppercase" style="font-size: 0.72rem;">Notes & Remarks:</span>
                <p class="small text-secondary mb-0 bg-body-tertiary p-2 rounded border">{{ $payment->notes ?? $payment->remarks }}</p>
            </div>
            @endif

            <!-- GCash Proof Preview if uploaded (screen only) -->
            @if($payment->receipt_path)
            <div class="mb-4 d-print-none no-print position-relative z-1">
                <span class="small text-secondary d-block fw-semibold mb-2">Attached Payment Proof:</span>
                <div class="border rounded p-2 text-center bg-light">
                    <img src="{{ asset('storage/' . $payment->receipt_path) }}" alt="GCash Screenshot" class="img-fluid rounded" style="max-height: 250px;">
                </div>
            </div>
            @endif

            <!-- Signatures / Official Stamp -->
            <div class="row pt-5 mt-3 border-top text-center position-relative z-1" style="border-color: var(--border-color) !important;">
                <div class="col-6">
                    <div class="border-bottom pb-2 mb-2 mx-auto" style="max-width: 220px; border-color: var(--text-primary) !important;">
                        <span class="fw-bold text-body">{{ $payment->tenant->full_name ?? ($payment->tenant->user->name ?? 'Tenant') }}</span>
                    </div>
                    <span class="small text-secondary">Tenant Signature</span>
                </div>
                <div class="col-6">
                    <div class="border-bottom pb-2 mb-2 mx-auto" style="max-width: 220px; border-color: var(--text-primary) !important;">
                        <span class="fw-bold text-primary">ADMINISTRATION</span>
                    </div>
                    <span class="small text-secondary">Authorized Signature & Stamp</span>
                </div>
            </div>

            <!-- Footer note -->
            <div class="text-center text-secondary small mt-4 pt-3 border-top position-relative z-1" style="font-size: 0.75rem; border-color: var(--border-color) !important;">
                Thank you for staying with us! This computer-generated receipt serves as official acknowledgment of rent payment.
            </div>
        </div>
    </div>
</div>
@endsection
