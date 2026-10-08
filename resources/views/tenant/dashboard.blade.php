@extends('layouts.tenant')

@section('title', 'Tenant Dashboard')

@section('content')
<!-- Hero Welcome Banner -->
<div class="hero-welcome-card p-4 mb-4 shadow-sm position-relative overflow-hidden animate-fade-up stagger-1">
    <div class="hero-welcome-bg-glow"></div>
    <div class="position-relative z-1">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start align-items-md-center gap-3 text-center text-sm-start">
                    <div class="position-relative profile-avatar-frame flex-shrink-0">
                        <img src="{{ auth()->user()->profile_picture_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle border border-3 border-white shadow" style="width: 82px; height: 82px; object-fit: cover;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0284c7&color=ffffff';">
                    </div>
                    <div>
                        <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2 mb-2 flex-wrap">
                            <span class="resident-status-badge">
                                <i class="bi bi-shield-check me-1"></i> Active Resident
                            </span>
                            <span class="tenant-id-badge font-monospace">
                                <i class="bi bi-person-badge me-1"></i> <span class="badge-label">ID:</span> <span class="tenant-id-highlight">{{ $tenant->tenant_code }}</span>
                            </span>
                        </div>
                        <h2 class="fw-bold mb-2 tenant-hero-title">Welcome {{ auth()->user()->name }}!</h2>
                        
                        <!-- High Contrast, Crystal Clear Personal Information Pills -->
                        <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2 flex-wrap">
                            <span class="tenant-hero-info-pill" title="Assigned Room" style="border-color: rgba(16, 185, 129, 0.4) !important;">
                                <i class="bi bi-door-open-fill" style="color: #059669 !important;"></i>
                                <span class="pill-text">Room {{ $room->room_number ?? 'Unassigned' }} ({{ $room->room_type ?? 'Standard' }})</span>
                            </span>
                            <span class="tenant-hero-info-pill" title="Move-in Date">
                                <i class="bi bi-calendar-check-fill pill-icon-date"></i>
                                <span class="pill-text">Resident since {{ $tenant->move_in_date ? $tenant->move_in_date->format('M d, Y') : 'N/A' }}</span>
                            </span>
                            @if($tenant->contact_number)
                            <span class="tenant-hero-info-pill" title="Contact Number">
                                <i class="bi bi-telephone-fill pill-icon-phone"></i>
                                <span class="pill-text">{{ $tenant->contact_number }}</span>
                            </span>
                            @endif
                            @if(auth()->user()->email)
                            <span class="tenant-hero-info-pill" title="Email Address">
                                <i class="bi bi-envelope-fill pill-icon-email"></i>
                                <span class="pill-text">{{ auth()->user()->email }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-end gap-2">
                    <a href="{{ route('tenant.payments.submit') }}" class="btn btn-light text-primary fw-bold shadow-sm quick-action-pill px-3 py-2 flex-grow-1 flex-sm-grow-0 text-center">
                        <i class="bi bi-wallet2 me-1"></i> SUBMIT A PAYMENT
                    </a>
                    <a href="{{ route('tenant.maintenance.create') }}" class="btn btn-outline-light fw-semibold quick-action-pill px-3 py-2 flex-grow-1 flex-sm-grow-0 text-center">
                        <i class="bi bi-tools me-1"></i> REPORT ISSUE
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if($pendingRoomRequest)
<div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4 animate-fade-up stagger-2" style="border-radius: 12px;">
    <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
    <div>
        <strong>Pending Room Request:</strong> You submitted a request to transfer to <strong>Room {{ $pendingRoomRequest->room->room_number }}</strong> on {{ $pendingRoomRequest->created_at->format('M d, Y') }}. The Admin is reviewing room availability.
    </div>
</div>
@endif

@php
    $rentStatus = $tenant->getRentStatusForMonthYear($currentMonth, $currentYear);
    $monthlyRent = $room ? (float) $room->monthly_rent : 0;
    
    // Only verified / approved payments count as paid rent
    $totalPaidThisMonth = (float) ($rentStatus['paid_amount'] ?? 0);
    
    // Check if there is a pending payment submitted waiting for admin approval
    $hasPendingPayment = $currentPayment && $currentPayment->status === 'pending';
    
    // Remaining balance strictly based on verified/settled payments
    $remainingBalance = max(0, $monthlyRent - $totalPaidThisMonth);

    // Paid in full condition: STRICTLY ONLY true if verified paid >= monthly rent (and rent > 0) OR rentStatus status is paid
    // NEVER true if the payment is waiting for approval!
    $isPaidInFull = ($monthlyRent > 0 && $totalPaidThisMonth >= $monthlyRent) || 
                    ($rentStatus['status'] === 'paid');

    // Partial condition: verified paid amount > 0 and balance remaining, NOT paid in full
    $isPartial = !$isPaidInFull && ($totalPaidThisMonth > 0 && $remainingBalance > 0);

    $paymentPct = $monthlyRent > 0 ? min(100, round(($totalPaidThisMonth / $monthlyRent) * 100)) : 0;
@endphp

{{-- PERSISTENT RENT ALERTS: Show when rent is unpaid, partial, or waiting for approval. REMOVED COMPLETELY once paid! --}}
@if(!$isPaidInFull && $monthlyRent > 0)
    @if($hasPendingPayment)
        {{-- WAITING FOR ADMIN APPROVAL BANNER (Refined warm amber) --}}
        <div class="alert border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between p-3 p-md-4 mb-4 gap-3 animate-fade-up stagger-2" style="border-radius: 14px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.28) !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="rent-alert-icon-box shadow-sm flex-shrink-0" style="background: rgba(245, 158, 11, 0.2); color: #b45309;">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1" style="letter-spacing: 0.5px; color: #b45309;">WAITING FOR ADMIN APPROVAL</h5>
                    <div class="text-secondary small">
                        Your payment of <strong>₱{{ number_format($currentPayment->amount, 2) }}</strong> (Reference: <code class="text-primary fw-semibold">{{ $currentPayment->gcash_reference ?: 'GCash' }}</code>) submitted on {{ $currentPayment->payment_date ? $currentPayment->payment_date->format('M d, Y') : $currentPayment->created_at->format('M d, Y') }} is currently awaiting verification by the Admin. Once verified, your rent status will be updated.
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('tenant.payments.index') }}" class="btn fw-semibold shadow-sm px-4 py-2" style="border-radius: 10px; background-color: #f59e0b; border: 1px solid #d97706; color: #ffffff;">
                    <i class="bi bi-clock-history me-1"></i> View Submitted Proof
                </a>
            </div>
        </div>
    @elseif($isPartial)
        {{-- PARTIALLY PAID ALERT: Settle remaining balance --}}
        <div class="alert alert-danger alert-permanent rent-alert-banner border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between p-3 p-md-4 mb-4 gap-3 animate-fade-up stagger-2">
            <div class="d-flex align-items-center gap-3">
                <div class="rent-alert-icon-box shadow-sm flex-shrink-0">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-danger mb-1" style="letter-spacing: 0.5px;">PLEASE PAY THE FULL AMOUNT</h4>
                    <div class="text-danger-emphasis">
                        You have made a partial payment of <strong>₱{{ number_format($totalPaidThisMonth, 2) }}</strong> for <strong>{{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F Y') }}</strong>. Please settle the remaining balance of <strong class="text-danger fs-6">₱{{ number_format($remainingBalance, 2) }}</strong> to complete your monthly rent.
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
        {{-- UNPAID ALERT: Pay monthly rent --}}
        <div class="alert alert-danger alert-permanent rent-alert-banner border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between p-3 p-md-4 mb-4 gap-3 animate-fade-up stagger-2">
            <div class="d-flex align-items-center gap-3">
                <div class="rent-alert-icon-box shadow-sm flex-shrink-0">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-danger mb-1" style="letter-spacing: 0.5px;">PLEASE PAY YOUR MONTHLY RENT</h4>
                    <div class="text-danger-emphasis">
                        Your monthly rent of <strong>₱{{ number_format($monthlyRent, 2) }}</strong> for <strong>{{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F Y') }}</strong> is not yet paid{{ $isOverdue ? ' (Overdue since ' . $nextDueDate->format('F d, Y') . ')' : '' }}. Please settle your payment promptly to keep your boarding house room active.
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

<!-- Key Status Cards -->
<div class="row g-3 mb-4">
    <!-- Assigned Room Card -->
    <div class="col-md-6 col-xl-4 animate-fade-up stagger-3">
        <div class="stat-card-pro accent-green h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Assigned Room</span>
                    <h3 class="fw-bold text-success mt-1 mb-0">
                        Room {{ $room->room_number ?? 'Unassigned' }}
                    </h3>
                </div>
                <div class="stat-icon-wrap bg-success-subtle text-success">
                    <i class="bi bi-door-open fs-4"></i>
                </div>
            </div>
            
            <div class="d-flex justify-content-between text-muted small py-2 border-bottom">
                <span>Room Type:</span>
                <strong style="color: var(--text-primary);">{{ $room->room_type ?? 'N/A' }}</strong>
            </div>
            <div class="d-flex justify-content-between text-muted small py-2 border-bottom">
                <span>Floor Level:</span>
                <strong style="color: var(--text-primary);">Floor {{ $room->floor ?? '1' }}</strong>
            </div>
            <div class="d-flex justify-content-between text-muted small py-2">
                <span>Monthly Rent:</span>
                <strong class="text-success fw-bold fs-6">₱{{ number_format($room->monthly_rent ?? 0, 2) }}</strong>
            </div>
            
            <div class="mt-3 pt-2">
                <a href="{{ route('tenant.my-room.index') }}" class="btn btn-sm btn-outline-success w-100 quick-action-pill" style="border-radius: 8px;">
                    View Room Details & Amenities <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Monthly Rent Status Card -->
    <div class="col-md-6 col-xl-4 animate-fade-up stagger-4">
        <div class="stat-card-pro {{ $isPaidInFull ? 'accent-green' : ($hasPendingPayment ? 'accent-amber' : ($isPartial ? 'accent-blue' : ($isOverdue ? 'accent-danger' : 'accent-amber'))) }} h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Current Month Rent</span>
                    @if($isPaidInFull)
                        <h3 class="fw-bold text-success mt-1 mb-0">PAID</h3>
                    @elseif($hasPendingPayment)
                        <h3 class="fw-bold text-warning mt-1 mb-0" style="font-size: 1.25rem;">WAITING FOR APPROVAL</h3>
                    @elseif($isPartial)
                        <h3 class="fw-bold text-info mt-1 mb-0">PARTIAL</h3>
                    @elseif($isOverdue)
                        <h3 class="fw-bold text-danger mt-1 mb-0">OVERDUE</h3>
                    @else
                        <h3 class="fw-bold text-danger mt-1 mb-0">PAYMENT DUE</h3>
                    @endif
                </div>
                <div class="stat-icon-wrap {{ $isPaidInFull ? 'bg-success-subtle text-success' : ($hasPendingPayment ? 'bg-warning-subtle text-warning' : ($isPartial ? 'bg-info-subtle text-info' : ($isOverdue ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning'))) }}">
                    <i class="bi {{ $isPaidInFull ? 'bi-shield-check' : ($hasPendingPayment ? 'bi-hourglass-split' : ($isPartial ? 'bi-pie-chart-fill' : ($isOverdue ? 'bi-exclamation-triangle-fill' : 'bi-clock-fill'))) }} fs-4"></i>
                </div>
            </div>

            <!-- Payment Progress Bar -->
            <div class="my-3">
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Rent Paid: ₱{{ number_format($totalPaidThisMonth, 2) }}</span>
                    <span class="fw-bold">{{ $paymentPct }}%</span>
                </div>
                <div class="progress" style="height: 8px; border-radius: 6px; background-color: var(--table-row-hover);">
                    <div class="progress-bar progress-bar-animated-pro {{ $isPaidInFull ? 'bg-success' : ($isPartial ? 'bg-info' : ($hasPendingPayment ? 'bg-warning' : 'bg-danger')) }}" 
                         data-progress="{{ $paymentPct }}%" 
                         style="width: 0%;"></div>
                </div>
            </div>

            <div class="d-flex justify-content-between text-muted small py-1 border-bottom">
                <span>Billing Period:</span>
                <strong style="color: var(--text-primary);">{{ \DateTime::createFromFormat('!m', $currentMonth)->format('F') }} {{ $currentYear }}</strong>
            </div>
            <div class="d-flex justify-content-between text-muted small py-1 border-bottom">
                <span>Due Date:</span>
                <strong class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}" style="{{ $isOverdue ? '' : 'color: var(--text-primary);' }}">{{ $nextDueDate->format('F d, Y') }}</strong>
            </div>
            <div class="d-flex justify-content-between text-muted small py-1">
                <span>Remaining:</span>
                <strong class="{{ $remainingBalance > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">₱{{ number_format($remainingBalance, 2) }}</strong>
            </div>

            <div class="mt-3 pt-1">
                @if($isPaidInFull)
                    <a href="{{ route('tenant.payments.index') }}" class="btn btn-sm btn-outline-success w-100 fw-semibold quick-action-pill" style="border-radius: 8px;">
                        <i class="bi bi-receipt me-1"></i> View Official Receipt
                    </a>
                @elseif($hasPendingPayment)
                    <a href="{{ route('tenant.payments.index') }}" class="btn btn-sm btn-warning text-dark w-100 fw-bold shadow-xs quick-action-pill" style="border-radius: 8px;">
                        <i class="bi bi-clock-history me-1"></i> Awaiting Admin Verification
                    </a>
                @else
                    <a href="{{ route('tenant.payments.submit') }}" class="btn btn-sm btn-success w-100 fw-bold shadow-sm quick-action-pill" style="border-radius: 8px;">
                        <i class="bi bi-wallet2 me-1"></i> SUBMIT A PAYMENT
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Maintenance Overview Card -->
    <div class="col-md-12 col-xl-4 animate-fade-up stagger-5">
        <div class="stat-card-pro accent-purple h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Maintenance Tickets</span>
                    <h3 class="fw-bold mt-1 mb-0" style="color: var(--text-primary);" data-counter="{{ $recentMaintenance->count() }}">
                        {{ $recentMaintenance->count() }}
                    </h3>
                </div>
                <div class="stat-icon-wrap bg-info-subtle text-info">
                    <i class="bi bi-wrench-adjustable fs-4"></i>
                </div>
            </div>

            <p class="text-muted small mb-3">
                Need repairs for plumbing, electricals, or furniture in your room? File a maintenance request directly to the Admin.
            </p>

            <div class="d-grid gap-2 mt-auto">
                <a href="{{ route('tenant.maintenance.create') }}" class="btn btn-sm btn-outline-primary quick-action-pill" style="border-radius: 8px;">
                    <i class="bi bi-tools me-1"></i> REPORT ISSUE
                </a>
                <a href="{{ route('tenant.maintenance.index') }}" class="btn btn-sm btn-outline-secondary quick-action-pill" style="border-radius: 8px;">
                    View All Tickets <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Personal & Tenancy Information Section -->
<div class="custom-card mb-4 border-0 shadow-sm animate-fade-up">
    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2" style="border-color: var(--border-color) !important;">
        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
            <i class="bi bi-person-lines-fill info-tile-icon me-2"></i> Personal & Tenancy Information
        </h5>
        <a href="{{ route('tenant.settings.index') }}" class="btn btn-sm btn-outline-primary quick-action-pill" style="border-radius: 8px;">
            <i class="bi bi-pencil me-1"></i> Edit Profile
        </a>
    </div>

    <div class="row g-3">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-tile">
                <div class="info-tile-label"><i class="bi bi-person info-tile-icon"></i> Full Name</div>
                <div class="info-tile-value">{{ auth()->user()->name }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-tile">
                <div class="info-tile-label"><i class="bi bi-envelope info-tile-icon"></i> Email Address</div>
                <div class="info-tile-value text-truncate" title="{{ auth()->user()->email }}">{{ auth()->user()->email }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-tile">
                <div class="info-tile-label"><i class="bi bi-telephone info-tile-icon"></i> Mobile Number</div>
                <div class="info-tile-value">{{ $tenant->contact_number ?: 'Not provided' }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-tile">
                <div class="info-tile-label"><i class="bi bi-person-badge info-tile-icon"></i> Tenant ID Code</div>
                <div class="info-tile-value font-monospace tenant-id-highlight">{{ $tenant->tenant_code }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-tile">
                <div class="info-tile-label"><i class="bi bi-door-open info-tile-icon"></i> Assigned Room</div>
                <div class="info-tile-value">Room {{ $room->room_number ?? 'Unassigned' }} ({{ $room->room_type ?? 'Standard' }})</div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-tile">
                <div class="info-tile-label"><i class="bi bi-calendar-event info-tile-icon"></i> Move-in Date</div>
                <div class="info-tile-value">{{ $tenant->move_in_date ? $tenant->move_in_date->format('F d, Y') : 'N/A' }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-tile">
                <div class="info-tile-label"><i class="bi bi-telephone-plus info-tile-icon"></i> Emergency Contact</div>
                <div class="info-tile-value">{{ $tenant->emergency_contact ?: 'None provided' }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-tile">
                <div class="info-tile-label"><i class="bi bi-geo-alt info-tile-icon"></i> Home Address</div>
                <div class="info-tile-value text-truncate" title="{{ $tenant->address ?: 'None provided' }}">{{ $tenant->address ?: 'None provided' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Payments Table -->
    <div class="col-lg-7 animate-fade-up stagger-6">
        <div class="custom-card mb-4 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2" style="border-color: var(--border-color) !important;">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-credit-card-2-front text-primary me-2"></i> Recent Rent Payments
                </h5>
                <a href="{{ route('tenant.payments.index') }}" class="text-primary text-decoration-none small fw-semibold">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            
            <div class="table-responsive">
                <div class="table-scroll-hint">
                    <i class="bi bi-arrows-expand"></i> Swipe table horizontally to see all columns
                </div>
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Period</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-end">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                        <tr>
                            <td class="fw-semibold">{{ $payment->billing_period }}</td>
                            <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $payment->payment_method === 'cash' ? 'bg-secondary' : 'bg-primary' }}">
                                    {{ strtoupper($payment->payment_method) }}
                                </span>
                            </td>
                            <td class="fw-bold text-success">₱{{ number_format($payment->amount, 2) }}</td>
                            <td class="text-nowrap">
                                <div class="d-flex align-items-center gap-1">
                                    @if(in_array($payment->status, ['verified', 'paid']))
                                        <span class="badge-pill badge-success" style="font-size: 0.72rem;">Paid</span>
                                    @elseif($payment->status === 'partial')
                                        <span class="badge-pill badge-warning" style="font-size: 0.72rem;">Partial</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="badge-pill badge-warning" style="font-size: 0.72rem;">Pending</span>
                                    @else
                                        <span class="badge-pill badge-danger" style="font-size: 0.72rem;">Rejected</span>
                                    @endif

                                    @if($payment->is_edited)
                                        <button type="button" class="badge-edited border-0 ms-1" 
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
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1" style="font-size: 0.72rem;" title="Receipt available once payment is verified and approved by the landlord">
                                            <i class="bi bi-hourglass-split me-1"></i> Pending
                                        </span>
                                    @else
                                        <a href="{{ route('tenant.payments.receipt', $payment->id) }}" class="btn btn-sm btn-outline-secondary" title="View Receipt">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                    @endif
                                    @if($payment->is_edited)
                                        <button type="button" class="btn btn-sm btn-outline-purple" 
                                                onclick="openTenantPaymentEditHistoryModal({{ $payment->id }})" 
                                                title="View Revision Log">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-wallet2 fs-2 d-block mb-1"></i>
                                No payment records found yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Boarding House Rules Summary -->
        <div class="custom-card border-0 shadow-sm animate-fade-up stagger-7">
            <div class="border-bottom pb-2 mb-3" style="border-color: var(--border-color) !important;">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-shield-check text-success me-2"></i> Boarding House Guidelines
                </h5>
            </div>
            <ul class="list-unstyled mb-0 small text-muted">
                <li class="mb-2 d-flex align-items-center">
                    <i class="bi bi-clock text-primary me-2 fs-5"></i>
                    <span><strong>Main Gate Curfew:</strong> Closed at 10:00 PM for security. Contact Admin for emergency late entries.</span>
                </li>
                <li class="mb-2 d-flex align-items-center">
                    <i class="bi bi-volume-mute text-primary me-2 fs-5"></i>
                    <span><strong>Quiet Hours:</strong> 10:00 PM to 6:00 AM daily. Please respect fellow boarders.</span>
                </li>
                <li class="mb-2 d-flex align-items-center">
                    <i class="bi bi-trash text-primary me-2 fs-5"></i>
                    <span><strong>Clean As You Go (CLAYGO):</strong> Always clean up common areas and dispose of waste properly.</span>
                </li>
                <li class="d-flex align-items-center">
                    <i class="bi bi-person-check text-primary me-2 fs-5"></i>
                    <span><strong>Visitors Policy:</strong> Day visitors allowed until 8:00 PM in the reception lobby only.</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Recent Maintenance Tickets -->
    <div class="col-lg-5 animate-fade-up stagger-6">
        <div class="custom-card mb-4 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2" style="border-color: var(--border-color) !important;">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-tools text-warning me-2"></i> Maintenance Tickets
                </h5>
                <a href="{{ route('tenant.maintenance.create') }}" class="btn btn-sm btn-primary quick-action-pill" style="border-radius: 8px;">
                    <i class="bi bi-plus-circle me-1"></i> Report
                </a>
            </div>
            
            <div class="list-group list-group-flush">
                @forelse($recentMaintenance as $item)
                <a href="{{ route('tenant.maintenance.show', $item->id) }}" class="list-group-item list-group-item-action py-3 px-2 border-bottom">
                    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                        <h6 class="mb-0 fw-bold">{{ $item->title }}</h6>
                        @if($item->status === 'resolved')
                            <span class="badge-pill badge-success" style="font-size: 0.7rem;">Resolved</span>
                        @elseif($item->status === 'in_progress')
                            <span class="badge-pill badge-info" style="font-size: 0.7rem;">In Progress</span>
                        @elseif($item->status === 'rejected')
                            <span class="badge-pill badge-danger" style="font-size: 0.7rem;">Rejected</span>
                        @else
                            <span class="badge-pill badge-warning" style="font-size: 0.7rem;">Pending</span>
                        @endif
                    </div>
                    <p class="mb-1 text-muted small text-truncate">{{ $item->description }}</p>
                    <small class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i> {{ $item->created_at->format('M d, Y') }} &bull; Priority: 
                        <span class="badge {{ $item->priority === 'High' ? 'bg-danger' : ($item->priority === 'Medium' ? 'bg-warning text-dark' : 'bg-secondary') }}" style="font-size: 0.65rem;">
                            {{ ucfirst($item->priority) }}
                        </span>
                    </small>
                </a>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check2-circle fs-2 d-block mb-1 text-success"></i>
                    No active maintenance issues reported.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@include('tenant.payments.partials.edit-history-modal')
@endsection

