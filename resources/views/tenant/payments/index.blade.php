@extends('layouts.tenant')

@section('title', 'My Payment History')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h1 class="h3 fw-bold mb-1">My Rent Payments</h1>
        <p class="text-muted mb-0">Track all your submitted payments, verification statuses, and download official receipts.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('tenant.payments.submit') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-wallet2 me-1"></i> SUBMIT A PAYMENT
        </a>
    </div>
</div>

@php
    $rentStatus = $tenant->getRentStatusForMonthYear($currentMonth ?? date('n'), $currentYear ?? date('Y'));
    $monthlyRent = $tenant->room ? (float) $tenant->room->monthly_rent : 0;
    $totalPaidThisMonth = (float) ($rentStatus['paid_amount'] ?? 0);
    $hasPendingPayment = isset($currentPayment) && $currentPayment && $currentPayment->status === 'pending';
    $remainingBalance = max(0, $monthlyRent - $totalPaidThisMonth);

    // Paid in full condition: ONLY true if verified paid >= monthly rent (and rent > 0) OR rentStatus status is paid
    // NEVER true if waiting for approval!
    $isPaidInFull = ($monthlyRent > 0 && $totalPaidThisMonth >= $monthlyRent) || 
                    ($rentStatus['status'] === 'paid');

    $isPartial = !$isPaidInFull && ($totalPaidThisMonth > 0 && $remainingBalance > 0);
@endphp

{{-- PERSISTENT RENT ALERTS: Dismissed completely once paid! --}}
@if(!$isPaidInFull && $monthlyRent > 0)
    @if($hasPendingPayment)
        <div class="alert alert-warning border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between p-3 p-md-4 mb-4 gap-3 animate-fade-up" style="border-radius: 14px; background: rgba(245, 158, 11, 0.12); border-left: 5px solid #f59e0b !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="rent-alert-icon-box shadow-sm flex-shrink-0" style="background: rgba(245, 158, 11, 0.2); color: #b45309;">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-warning-emphasis mb-1" style="letter-spacing: 0.5px;">PAYMENT WAITING FOR APPROVAL</h5>
                    <div class="text-secondary small">
                        Your submitted payment of <strong>₱{{ number_format($currentPayment->amount, 2) }}</strong> (Reference: <code class="text-primary fw-semibold">{{ $currentPayment->gcash_reference ?: 'Submitted' }}</code>) is currently being reviewed by Admin. Once approved, your rent balance will be updated.
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0">
                <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
                    <i class="bi bi-hourglass-split me-1"></i> Waiting for Approval
                </span>
            </div>
        </div>
    @elseif($isPartial)
        <div class="alert alert-danger alert-permanent rent-alert-banner border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between p-3 p-md-4 mb-4 gap-3 animate-fade-up">
            <div class="d-flex align-items-center gap-3">
                <div class="rent-alert-icon-box shadow-sm flex-shrink-0">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-danger mb-1" style="letter-spacing: 0.5px;">PLEASE PAY THE FULL AMOUNT</h5>
                    <div class="text-danger-emphasis small">
                        You have made a partial payment of <strong>₱{{ number_format($totalPaidThisMonth, 2) }}</strong> for <strong>{{ \Carbon\Carbon::createFromDate($currentYear ?? date('Y'), $currentMonth ?? date('n'), 1)->format('F Y') }}</strong>. Please settle the remaining balance of <strong class="text-danger">₱{{ number_format($remainingBalance, 2) }}</strong> to complete your monthly rent.
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('tenant.payments.submit', ['method' => 'gcash']) }}" class="btn btn-danger fw-bold shadow-sm px-4 py-2" style="border-radius: 10px;">
                    <i class="bi bi-wallet2 me-1"></i> Pay Now via GCash
                </a>
            </div>
        </div>
    @else
        <div class="alert alert-danger alert-permanent rent-alert-banner border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between p-3 p-md-4 mb-4 gap-3 animate-fade-up">
            <div class="d-flex align-items-center gap-3">
                <div class="rent-alert-icon-box shadow-sm flex-shrink-0">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-danger mb-1" style="letter-spacing: 0.5px;">PLEASE PAY YOUR MONTHLY RENT</h5>
                    <div class="text-danger-emphasis small">
                        Your monthly rent of <strong>₱{{ number_format($monthlyRent, 2) }}</strong> for <strong>{{ \Carbon\Carbon::createFromDate($currentYear ?? date('Y'), $currentMonth ?? date('n'), 1)->format('F Y') }}</strong> is not yet paid. Please settle your payment promptly to keep your boarding house room active.
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('tenant.payments.submit', ['method' => 'gcash']) }}" class="btn btn-danger fw-bold shadow-sm px-4 py-2" style="border-radius: 10px;">
                    <i class="bi bi-wallet2 me-1"></i> Pay Now via GCash
                </a>
            </div>
        </div>
    @endif
@endif

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4 animate-fade-up stagger-1">
        <div class="stat-card-pro accent-green">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Total Settled Rent</span>
                    <h3 class="fw-bold text-success mt-1 mb-0" data-counter-currency="{{ $totalPaid }}">₱{{ number_format($totalPaid, 2) }}</h3>
                    <small class="text-muted">Verified by Admin</small>
                </div>
                <div class="stat-icon-wrap rounded-circle bg-success-subtle text-success">
                    <i class="bi bi-shield-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 animate-fade-up stagger-2">
        <div class="stat-card-pro accent-amber">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">In Verification</span>
                    <h3 class="fw-bold text-warning mt-1 mb-0" data-counter="{{ $pendingPaymentsCount }}">{{ $pendingPaymentsCount }}</h3>
                    <small class="text-muted">Awaiting Admin confirmation</small>
                </div>
                <div class="stat-icon-wrap rounded-circle bg-warning-subtle text-warning">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 animate-fade-up stagger-3">
        <div class="stat-card-pro accent-blue">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Monthly Rent Rate</span>
                    <h3 class="fw-bold text-primary mt-1 mb-0" data-counter-currency="{{ $tenant->room->monthly_rent ?? 0 }}">
                        ₱{{ number_format($tenant->room->monthly_rent ?? 0, 2) }}
                    </h3>
                    <small class="text-muted">Room {{ $tenant->room->room_number ?? 'N/A' }}</small>
                </div>
                <div class="stat-icon-wrap rounded-circle bg-primary-subtle text-primary">
                    <i class="bi bi-door-open fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payments Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3">
        <h5 class="fw-bold mb-0">Payment Records</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint">
                <i class="bi bi-arrows-expand"></i> Swipe table horizontally to see all columns
            </div>
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Receipt #</th>
                        <th>Billing Period</th>
                        <th>Payment Date & Time</th>
                        <th>Method</th>
                        <th>Reference / Notes</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="fw-semibold text-primary">#REC-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="fw-semibold">{{ $payment->billing_period }}</td>
                        <td>
                            <div>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A' }}</div>
                            <div class="text-secondary" style="font-size: 0.72rem;">
                                <i class="bi bi-clock me-1"></i>{{ $payment->formatted_payment_time }}
                            </div>
                        </td>
                        <td>
                            @if($payment->payment_method === 'cash')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-cash me-1"></i> Cash
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                    <i class="bi bi-wallet2 me-1"></i> GCash
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($payment->gcash_reference)
                                <span class="font-monospace small">{{ $payment->gcash_reference }}</span>
                            @else
                                <span class="text-muted small">{{ $payment->notes ?: '—' }}</span>
                            @endif
                        </td>
                        <td class="fw-bold" style="color: var(--text-primary);">₱{{ number_format($payment->amount, 2) }}</td>
                        <td class="text-nowrap">
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                @if(in_array($payment->status, ['paid', 'verified']))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle me-1"></i> Paid
                                    </span>
                                @elseif($payment->status === 'partial')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                                        <i class="bi bi-pie-chart-fill me-1"></i> Partial
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                        <i class="bi bi-hourglass-split me-1"></i> Waiting for Approval
                                    </span>
                                @else
                                    <div>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="bi bi-x-circle me-1"></i> Rejected
                                        </span>
                                        @if($payment->rejection_reason)
                                            <div class="small text-danger mt-1">Reason: {{ $payment->rejection_reason }}</div>
                                        @endif
                                    </div>
                                @endif

                                @if($payment->is_edited)
                                    <button type="button" class="badge-edited border-0" 
                                            onclick="openTenantPaymentEditHistoryModal({{ $payment->id }})" 
                                            title="Payment adjusted by Landlord/Admin. Click to view edit history & changes">
                                        <i class="bi bi-clock-history"></i> EDITED
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="table-actions-nowrap d-inline-flex align-items-center gap-1">
                                @if($payment->status === 'pending')
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1" style="font-size: 0.75rem;" title="Receipt will be generated after payment is verified and approved by the landlord">
                                        <i class="bi bi-hourglass-split me-1"></i> Receipt Pending
                                    </span>
                                @else
                                    <a href="{{ route('tenant.payments.receipt', $payment->id) }}" class="btn btn-sm btn-outline-secondary text-nowrap" title="View Official Receipt">
                                        <i class="bi bi-receipt me-1"></i> View Receipt
                                    </a>
                                @endif
                                @if($payment->is_edited)
                                    <button type="button" class="btn btn-sm btn-outline-purple text-nowrap" 
                                            onclick="openTenantPaymentEditHistoryModal({{ $payment->id }})" 
                                            title="View Landlord Revision History">
                                        <i class="bi bi-clock-history me-1"></i> History
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No rent payments submitted yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer bg-transparent py-3">
        {{ $payments->links() }}
    </div>
    @endif
</div>

@include('tenant.payments.partials.edit-history-modal')
@endsection

