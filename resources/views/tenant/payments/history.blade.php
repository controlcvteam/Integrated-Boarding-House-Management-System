@extends('layouts.tenant')

@section('title', 'Payment Revisions - #' . ($payment->payment_code ?? ('PAY-' . $payment->id)))

@section('content')
<div class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('tenant.payments.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="border-radius: 8px;">
                    <i class="bi bi-chevron-left"></i> Back to Payments
                </a>
                <span class="badge bg-purple text-white px-2 py-1 font-monospace" style="font-size: 0.78rem;">
                    {{ $payment->payment_code ?? ('PAY-' . $payment->id) }}
                </span>
                <span class="badge-edited" style="cursor: default;">
                    <i class="bi bi-clock-history"></i> EDITED BY ADMIN
                </span>
            </div>
            <h4 class="fw-bold mb-1" style="color: var(--text-primary); font-size: 1.35rem;">
                Payment Modification & Revision History
            </h4>
            <p class="text-secondary mb-0 small">
                Complete, immutable audit log of adjustments made by Property Administration for your payment.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tenant.payments.receipt', $payment->id) }}" class="btn btn-sm btn-primary-custom py-2 px-3">
                <i class="bi bi-receipt me-1"></i> View Official Receipt
            </a>
            <button type="button" class="btn btn-sm btn-secondary-custom py-2 px-3" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print History
            </button>
        </div>
    </div>
</div>

<!-- Transparency Banner -->
<div class="alert alert-purple border-0 shadow-xs p-3 p-md-4 mb-4 d-flex align-items-start gap-3" 
     style="background: rgba(124, 58, 237, 0.08); border-left: 4px solid #7c3aed !important; border-radius: 12px;">
    <div class="p-2 rounded-circle bg-purple-subtle text-purple flex-shrink-0" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-shield-check fs-4"></i>
    </div>
    <div>
        <h6 class="fw-bold mb-1" style="color: #7c3aed;">
            Permanent Audit & Financial Accountability
        </h6>
        <p class="text-secondary mb-0 small">
            To prevent accidental data loss and protect both tenants and landlords, payments cannot be deleted. Any adjustments made by the property owner (e.g., corrected amount, rental period adjustment, or payment method reconciliation) are permanently tracked below with reasons, timestamps, and previous values.
        </p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Card 1: Current Active Payment -->
    <div class="col-lg-6">
        <div class="custom-card h-100 shadow-sm border-0">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                <h6 class="fw-bold mb-0 text-primary" style="font-size: 0.95rem;">
                    <i class="bi bi-check2-circle me-1"></i> Current Active Payment Details
                </h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.72rem;">
                    CURRENT
                </span>
            </div>

            <div class="row g-2 small">
                <div class="col-5 text-secondary">Payment Code:</div>
                <div class="col-7 fw-semibold font-monospace text-primary">{{ $payment->payment_code ?? ('PAY-' . $payment->id) }}</div>

                <div class="col-5 text-secondary">Receipt Number:</div>
                <div class="col-7 font-monospace fw-semibold">#REC-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</div>

                <div class="col-5 text-secondary">Assigned Room:</div>
                <div class="col-7 fw-semibold">Room {{ $payment->room->room_number ?? 'N/A' }} ({{ $payment->room->room_type ?? 'Standard' }})</div>

                <div class="col-5 text-secondary">Current Amount:</div>
                <div class="col-7 fw-bold text-success fs-6">₱{{ number_format($payment->amount, 2) }}</div>

                <div class="col-5 text-secondary">Rental Period:</div>
                <div class="col-7 fw-semibold">{{ $payment->billing_period_label }}</div>

                <div class="col-5 text-secondary">Payment Method:</div>
                <div class="col-7">
                    <span class="badge {{ $payment->payment_method === 'cash' ? 'bg-secondary' : 'bg-primary' }}">
                        {{ strtoupper($payment->payment_method) }}
                    </span>
                    @if($payment->gcash_reference)
                        <span class="text-secondary font-monospace ms-1 small">({{ $payment->gcash_reference }})</span>
                    @endif
                </div>

                <div class="col-5 text-secondary">Payment Date:</div>
                <div class="col-7">{{ $payment->payment_date ? $payment->payment_date->format('F d, Y') : 'N/A' }}</div>

                <div class="col-5 text-secondary">Current Status:</div>
                <div class="col-7">
                    @if(in_array($payment->status, ['paid', 'verified']))
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            <i class="bi bi-check-circle me-1"></i> Paid
                        </span>
                    @elseif($payment->status === 'partial')
                        <span class="badge bg-info-subtle text-info border border-info-subtle">
                            <i class="bi bi-pie-chart-fill me-1"></i> Partial
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                            <i class="bi bi-hourglass-split me-1"></i> {{ ucfirst($payment->status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Original Initial Record -->
    <div class="col-lg-6">
        <div class="custom-card h-100 shadow-sm border-0" style="background-color: var(--table-header-bg); border: 1px dashed rgba(124, 58, 237, 0.35) !important;">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                <h6 class="fw-bold mb-0 text-secondary" style="font-size: 0.95rem;">
                    <i class="bi bi-clock-history me-1"></i> Original Entry (Before Any Edits)
                </h6>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1" style="font-size: 0.72rem;">
                    ORIGINAL RECORD
                </span>
            </div>

            <div class="row g-2 small">
                <div class="col-5 text-secondary">Initial Amount:</div>
                <div class="col-7 fw-bold text-secondary">
                    ₱{{ number_format($originalSnapshot['amount'] ?? $payment->amount, 2) }}
                </div>

                <div class="col-5 text-secondary">Initial Rental Period:</div>
                <div class="col-7 fw-semibold">
                    {{ $originalSnapshot['rental_period'] ?? $payment->billing_period_label }}
                </div>

                <div class="col-5 text-secondary">Initial Method:</div>
                <div class="col-7">
                    <span class="badge bg-secondary">
                        {{ strtoupper($originalSnapshot['payment_method'] ?? $payment->payment_method) }}
                    </span>
                    @if(!empty($originalSnapshot['gcash_reference']))
                        <span class="text-secondary font-monospace ms-1 small">({{ $originalSnapshot['gcash_reference'] }})</span>
                    @endif
                </div>

                <div class="col-5 text-secondary">Initial Date:</div>
                <div class="col-7">
                    {{ isset($originalSnapshot['payment_date']) ? \Carbon\Carbon::parse($originalSnapshot['payment_date'])->format('F d, Y') : ($payment->payment_date ? $payment->payment_date->format('F d, Y') : 'N/A') }}
                </div>

                <div class="col-5 text-secondary">Initial Status:</div>
                <div class="col-7">
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                        {{ ucfirst($originalSnapshot['status'] ?? $payment->status) }}
                    </span>
                </div>

                <div class="col-5 text-secondary">Date Created:</div>
                <div class="col-7 text-secondary">
                    {{ $payment->created_at->format('F j, Y — g:i A') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revisions Log Section -->
<div class="custom-card shadow-sm border-0 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
        <div>
            <h5 class="fw-bold mb-1" style="color: var(--text-primary);">
                <i class="bi bi-list-columns-reverse text-purple me-2"></i> All Revisions ({{ $histories->count() }} Total)
            </h5>
            <div class="text-secondary small">
                Every modification is listed below from latest to oldest with verified reasons from the landlord.
            </div>
        </div>
    </div>

    @forelse($histories as $idx => $history)
        @php
            $editNumber = $histories->count() - $idx;
        @endphp
        <div class="audit-entry-card is-edit mb-4 shadow-xs">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                <div>
                    <span class="badge bg-purple text-white fw-bold px-2 py-1 mb-1" style="font-size: 0.75rem;">
                        Edit #{{ $editNumber }}
                    </span>
                    <div class="fw-bold fs-6" style="color: var(--text-primary);">
                        {{ $history->created_at->format('F j, Y — g:i A') }}
                    </div>
                </div>
                <div class="text-end">
                    <span class="text-secondary small d-block">Adjusted By:</span>
                    <span class="fw-semibold text-primary">
                        <i class="bi bi-person-badge me-1"></i>{{ $history->editor_display_name }}
                    </span>
                </div>
            </div>

            <!-- Mandatory Reason Box -->
            <div class="mb-3 p-3 rounded" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                <span class="fw-bold text-warning text-uppercase small d-block mb-1" style="letter-spacing: 0.5px; font-size: 0.72rem;">
                    <i class="bi bi-chat-left-quote me-1"></i> Landlord Reason for Edit:
                </span>
                <div class="fw-semibold fst-italic" style="color: var(--text-primary); font-size: 0.95rem;">
                    "{{ $history->reason }}"
                </div>
            </div>

            <!-- Exact Field Changes -->
            <div class="fw-bold small text-uppercase text-secondary mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                Exact Changes (Before vs After):
            </div>

            @if(!empty($history->changed_fields))
                @foreach($history->changed_fields as $field => $change)
                    <div class="before-after-box mb-2">
                        <div>
                            <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.68rem;">
                                {{ $change['field_label'] ?? $field }} &bull; BEFORE
                            </div>
                            <div class="before-pill mt-1">
                                {{ $change['old'] ?? '—' }}
                            </div>
                        </div>
                        <div class="text-secondary px-2 fs-5">&rarr;</div>
                        <div>
                            <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.68rem;">
                                {{ $change['field_label'] ?? $field }} &bull; AFTER
                            </div>
                            <div class="after-pill mt-1">
                                {{ $change['new'] ?? '—' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-secondary small fst-italic">No specific field values modified.</div>
            @endif
        </div>
    @empty
        <div class="text-center py-5 text-secondary">
            <i class="bi bi-clock-history fs-1 d-block mb-2 text-muted"></i>
            <h6 class="fw-semibold">No edit history recorded yet</h6>
            <p class="small mb-0">This payment has not undergone any administrative edits.</p>
        </div>
    @endforelse
</div>
@endsection
