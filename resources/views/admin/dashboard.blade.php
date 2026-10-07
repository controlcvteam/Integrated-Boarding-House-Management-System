@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Overview of boarding house operations, room occupancies, and collections')

@section('content')
<!-- Header Quick Action Bar -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 animate-fade-up stagger-1">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fw-semibold" style="border-radius: 9999px; font-size: 0.8rem;">
            <i class="bi bi-person-fill me-1"></i> Welcome {{ auth()->user()->name }}!
        </span>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('admin.rooms.create') }}" class="btn btn-sm btn-outline-primary quick-action-pill fw-semibold" style="border-radius: 8px;">
            <i class="bi bi-plus-circle me-1"></i> Add Room
        </a>
        <a href="{{ route('admin.pending-tenants.index') }}" class="btn btn-sm btn-outline-warning quick-action-pill fw-semibold position-relative" style="border-radius: 8px;">
            <i class="bi bi-person-check me-1"></i> Pending Applicants
            @if($pendingTenantsCount > 0)
                <span class="badge bg-danger rounded-circle p-1 ms-1" style="font-size: 0.65rem;">{{ $pendingTenantsCount }}</span>
            @endif
        </a>
    </div>
</div>

<!-- Pending Tenant Action Notice Banner (if any) -->
@if($pendingTenantsCount > 0)
    <div class="alert alert-warning py-3 px-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2 animate-fade-up stagger-2" style="border-radius: 12px; border-left: 5px solid #f59e0b;">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-icon-wrap" style="width: 44px; height: 44px; background-color: #f59e0b; color: white;">
                <i class="bi bi-person-exclamation fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0" style="color: #92400e;">{{ $pendingTenantsCount }} Pending Tenant Registration(s) Awaiting Review</h6>
                <div style="font-size: 0.85rem; color: #b45309;">New tenant applicants are waiting for room assignment and approval.</div>
            </div>
        </div>
        <a href="{{ route('admin.pending-tenants.index') }}" class="btn btn-sm btn-dark px-3 py-2 fw-semibold quick-action-pill" style="border-radius: 8px;">
            <i class="bi bi-arrow-right-circle me-1"></i> Review Applications
        </a>
    </div>
@endif

<!-- Row 1: Primary Stats Cards -->
<div class="row g-3 mb-4">
    <!-- Pending Registrations Card (Clickable) -->
    <div class="col-sm-6 col-lg-3 animate-fade-up stagger-2">
        <a href="{{ route('admin.pending-tenants.index') }}" class="text-decoration-none">
            <div class="stat-card-pro accent-amber d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary fw-semibold" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">Pending Registrations</div>
                    <div class="fw-bold fs-3 mt-1" style="color: {{ $pendingTenantsCount > 0 ? '#f59e0b' : 'var(--text-primary)' }};" data-counter="{{ $pendingTenantsCount }}">
                        {{ $pendingTenantsCount }}
                    </div>
                    <div class="text-secondary" style="font-size: 0.76rem;">Requires review & approval</div>
                </div>
                <div class="stat-icon-wrap" style="background-color: #fef3c7; color: #b45309;">
                    <i class="bi bi-person-plus-fill fs-4"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Active Tenants Card -->
    <div class="col-sm-6 col-lg-3 animate-fade-up stagger-3">
        <a href="{{ route('admin.tenants.index') }}" class="text-decoration-none">
            <div class="stat-card-pro accent-blue d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary fw-semibold" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">Active Tenants</div>
                    <div class="fw-bold fs-3 mt-1" style="color: var(--text-primary);">
                        <span data-counter="{{ $activeTenants }}">{{ $activeTenants }}</span> 
                        <span class="text-secondary fw-normal fs-6">/ {{ $totalTenants }} total</span>
                    </div>
                    <div class="text-secondary" style="font-size: 0.76rem;">Current boarding boarders</div>
                </div>
                <div class="stat-icon-wrap" style="background-color: #e0f2fe; color: #0284c7;">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Rooms Availability Card -->
    <div class="col-sm-6 col-lg-3 animate-fade-up stagger-4">
        <a href="{{ route('admin.rooms.index') }}" class="text-decoration-none">
            <div class="stat-card-pro accent-green d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary fw-semibold" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">Available Rooms</div>
                    <div class="fw-bold fs-3 mt-1 text-success">
                        <span data-counter="{{ $availableRooms }}">{{ $availableRooms }}</span> 
                        <span class="text-secondary fw-normal fs-6">/ {{ $totalRooms }} rooms</span>
                    </div>
                    <div class="text-secondary" style="font-size: 0.76rem;">{{ $occupiedRooms }} occupied / full</div>
                </div>
                <div class="stat-icon-wrap" style="background-color: #dcfce7; color: #16a34a;">
                    <i class="bi bi-door-open-fill fs-4"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Pending GCash Card -->
    <div class="col-sm-6 col-lg-3 animate-fade-up stagger-5">
        <a href="{{ route('admin.payments.index') }}?status=pending" class="text-decoration-none">
            <div class="stat-card-pro accent-purple d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary fw-semibold" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">Pending GCash</div>
                    <div class="fw-bold fs-3 mt-1" style="color: {{ $pendingGcashCount > 0 ? '#38bdf8' : 'var(--text-primary)' }};" data-counter="{{ $pendingGcashCount }}">
                        {{ $pendingGcashCount }}
                    </div>
                    <div class="text-secondary" style="font-size: 0.76rem;">Receipts awaiting verification</div>
                </div>
                <div class="stat-icon-wrap" style="background-color: #e0f2fe; color: #0369a1;">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Row 2: Financial Metrics for Current Month -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-lg-3 animate-fade-up stagger-2">
        <div class="stat-card-pro accent-purple">
            <div class="text-secondary fw-semibold" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">Expected Rent ({{ $calDate->format('M Y') }})</div>
            <div class="fw-bold fs-4 mt-1" style="color: var(--text-primary);" data-counter-currency="{{ $expectedMonthlyRent }}">₱{{ number_format($expectedMonthlyRent, 2) }}</div>
            <div class="text-secondary" style="font-size: 0.76rem;">From all active rooms</div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3 animate-fade-up stagger-3">
        <div class="stat-card-pro accent-green">
            <div class="text-secondary fw-semibold" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">Collected Rent</div>
            <div class="fw-bold fs-4 mt-1 text-success" data-counter-currency="{{ $paymentsThisMonth }}">₱{{ number_format($paymentsThisMonth, 2) }}</div>
            <div class="text-secondary" style="font-size: 0.76rem;">Verified payments received</div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3 animate-fade-up stagger-4">
        <div class="stat-card-pro {{ $outstandingRent > 0 ? 'accent-danger' : 'accent-green' }}">
            <div class="text-secondary fw-semibold" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">Outstanding Balance</div>
            <div class="fw-bold fs-4 mt-1 {{ $outstandingRent > 0 ? 'text-danger' : 'text-success' }}" data-counter-currency="{{ $outstandingRent }}">
                ₱{{ number_format($outstandingRent, 2) }}
            </div>
            <div class="text-secondary" style="font-size: 0.76rem;">Unpaid rent this month</div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3 animate-fade-up stagger-5">
        <div class="stat-card-pro {{ $overdueCount > 0 ? 'accent-danger' : 'accent-green' }}">
            <div class="text-secondary fw-semibold" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">Overdue Rent Accounts</div>
            <div class="fw-bold fs-4 mt-1 {{ $overdueCount > 0 ? 'text-danger' : 'text-success' }}">
                <span data-counter="{{ $overdueCount }}">{{ $overdueCount }}</span> <span class="fs-6 fw-normal text-secondary">tenants</span>
            </div>
            <div class="text-secondary" style="font-size: 0.76rem;">Due date elapsed with balance</div>
        </div>
    </div>
</div>

<!-- Monthly Rent Collection Performance Meter -->
@php
    $collectionRate = $expectedMonthlyRent > 0 ? min(100, round(($paymentsThisMonth / $expectedMonthlyRent) * 100, 1)) : 0;
@endphp
<div class="custom-card mb-4 p-3 animate-fade-up stagger-4" style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.04) 0%, rgba(16, 185, 129, 0.06) 100%); border-left: 4px solid #10b981;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-graph-up-arrow text-success fs-5"></i>
            <span class="fw-bold" style="color: var(--text-primary); font-size: 0.95rem;">Monthly Rent Collection Rate ({{ $calDate->format('F Y') }})</span>
        </div>
        <div class="small fw-semibold">
            <span class="text-success fw-bold">₱{{ number_format($paymentsThisMonth, 2) }}</span> 
            <span class="text-secondary">collected of</span> 
            <span class="fw-bold">₱{{ number_format($expectedMonthlyRent, 2) }}</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle ms-2 px-2 py-1">{{ $collectionRate }}% Collected</span>
        </div>
    </div>
    <div class="progress" style="height: 10px; border-radius: 6px; background-color: var(--table-row-hover);">
        <div class="progress-bar progress-bar-animated-pro bg-success" data-progress="{{ $collectionRate }}%" style="width: 0%;"></div>
    </div>
</div>

<!-- Row 3: Rent Due Calendar (Section 39, 40, 41) -->
<div class="custom-card mb-4 animate-fade-up stagger-5">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3 border-bottom pb-3" style="border-color: var(--border-color) !important;">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--text-primary); font-size: 1.25rem;">
                <i class="bi bi-calendar3 me-2 text-primary"></i> Rent Due Calendar
            </h4>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">
                Tracks rent obligations and payment statuses for all active tenants by their monthly due date.
            </p>
        </div>

        <!-- Month Navigation Controls (Section 39) -->
        <div class="d-flex align-items-center gap-2">
            @php
                $prevMonth = $calDate->copy()->subMonth();
                $nextMonth = $calDate->copy()->addMonth();
            @endphp
            <a href="{{ route('admin.dashboard', ['cal_month' => $prevMonth->month, 'cal_year' => $prevMonth->year]) }}" 
               class="btn btn-sm btn-outline-secondary quick-action-pill" style="border-radius: 6px;" title="Previous Month">
                <i class="bi bi-chevron-left"></i>
            </a>

            <span class="fw-bold px-2 fs-6" style="min-width: 140px; text-align: center;">
                {{ $calDate->format('F Y') }}
            </span>

            <a href="{{ route('admin.dashboard', ['cal_month' => $nextMonth->month, 'cal_year' => $nextMonth->year]) }}" 
               class="btn btn-sm btn-outline-secondary quick-action-pill" style="border-radius: 6px;" title="Next Month">
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary-custom ms-2 quick-action-pill" style="font-size: 0.8rem;">
                Today
            </a>
        </div>
    </div>

    <!-- Calendar Legend (Section 40) -->
    <div class="d-flex flex-wrap align-items-center gap-3 mb-3 p-2 rounded" style="background-color: var(--table-header-bg); font-size: 0.78rem;">
        <span class="fw-bold text-secondary">Legend:</span>
        <span class="d-flex align-items-center gap-1"><i class="bi bi-check-circle-fill text-success"></i> Paid in Full</span>
        <span class="d-flex align-items-center gap-1"><i class="bi bi-pie-chart-fill text-warning"></i> Partially Paid</span>
        <span class="d-flex align-items-center gap-1"><i class="bi bi-hourglass-split text-info"></i> Pending Verification</span>
        <span class="d-flex align-items-center gap-1"><i class="bi bi-exclamation-circle-fill text-danger"></i> Overdue / Due</span>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar-grid">
        <!-- Day Names -->
        <div class="calendar-day-header">Sun</div>
        <div class="calendar-day-header">Mon</div>
        <div class="calendar-day-header">Tue</div>
        <div class="calendar-day-header">Wed</div>
        <div class="calendar-day-header">Thu</div>
        <div class="calendar-day-header">Fri</div>
        <div class="calendar-day-header">Sat</div>

        <!-- Leading empty cells -->
        @for($i = 0; $i < $firstDayOfWeek; $i++)
            <div class="calendar-cell other-month"></div>
        @endfor

        <!-- Days of the month -->
        @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $isToday = ($calendarYear == now()->year && $calendarMonth == now()->month && $day == now()->day);
                $dayEvents = $calendarEvents[$day] ?? [];
            @endphp
            <div class="calendar-cell {{ $isToday ? 'today' : '' }}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="calendar-cell-date">{{ $day }}</span>
                    @if($isToday)
                        <span class="badge bg-primary" style="font-size: 0.65rem;">Today</span>
                    @endif
                </div>

                <div class="d-flex flex-column gap-1 overflow-hidden">
                    @foreach($dayEvents as $ev)
                        @php
                            $badgeClass = match($ev['status']) {
                                'paid' => 'bg-success text-white',
                                'pending_verification' => 'bg-info text-dark',
                                'upcoming' => 'badge-warning',
                                'partial' => 'badge-partial',
                                'due', 'overdue' => 'bg-danger text-white',
                                default => 'bg-secondary text-white'
                            };
                        @endphp
                        <button type="button" class="calendar-event-badge {{ $badgeClass }} p-1 px-2 text-start w-100" 
                                onclick='openCalendarEventModal(@json($ev), {{ $calendarMonth }}, {{ $calendarYear }})'
                                title="{{ $ev['tenant_name'] }} - Rm {{ $ev['room_number'] }} | Click to view payment details">
                            <div class="d-flex align-items-center gap-1 text-truncate">
                                <i class="bi bi-circle-fill" style="font-size: 0.45rem; flex-shrink: 0;"></i>
                                <span class="fw-bold text-truncate" style="font-size: 0.75rem;">{{ $ev['tenant_name'] }}</span>
                            </div>
                            @if($ev['status'] === 'partial')
                                <div class="text-truncate fw-semibold" style="font-size: 0.65rem;">
                                    Partially Paid (Paid: ₱{{ number_format($ev['paid_amount'], 2) }})
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        @endfor

        <!-- Trailing empty cells -->
        @php
            $totalCells = $firstDayOfWeek + $daysInMonth;
            $remaining = (7 - ($totalCells % 7)) % 7;
        @endphp
        @for($j = 0; $j < $remaining; $j++)
            <div class="calendar-cell other-month"></div>
        @endfor
    </div>
</div>

<!-- Row 4: Room Availability Overview (Section 33) -->
<div class="custom-card mb-4 animate-fade-up stagger-6">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--border-color) !important;">
        <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary); font-size: 1.15rem;">
                <i class="bi bi-door-closed me-2 text-info"></i> Room Availability Overview
            </h5>
            <p class="text-secondary mb-0" style="font-size: 0.82rem;">Overview of room capacity and current occupant assignments.</p>
        </div>
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-sm btn-link text-decoration-none quick-action-pill" style="color: #38bdf8; font-size: 0.85rem;">
            View All Rooms &rarr;
        </a>
    </div>

    <div class="row g-3">
        @forelse($allRooms as $rm)
            @php
                $occupancyRate = $rm->capacity > 0 ? min(100, round(($rm->current_occupancy / $rm->capacity) * 100)) : 0;
            @endphp
            <div class="col-sm-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.rooms.show', $rm->id) }}" class="text-decoration-none text-reset">
                    <div class="room-overview-card h-100">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 fs-6">Room {{ $rm->room_number }}</h6>
                                @if($rm->is_available)
                                    <span class="badge-pill badge-success" style="font-size: 0.7rem;">Available</span>
                                @else
                                    <span class="badge-pill badge-danger" style="font-size: 0.7rem;">{{ $rm->status_reason }}</span>
                                @endif
                            </div>

                            <div class="text-secondary small">{{ $rm->room_type }}</div>
                            <div class="fw-bold text-success mt-1" style="font-size: 0.88rem;">₱{{ number_format($rm->monthly_rent, 2) }}<span class="text-secondary fw-normal">/mo</span></div>

                            <!-- Occupancy Progress Bar -->
                            <div class="my-2">
                                <div class="d-flex justify-content-between text-secondary" style="font-size: 0.72rem;">
                                    <span>Occupancy</span>
                                    <span class="fw-bold {{ $rm->available_slots > 0 ? 'text-primary' : 'text-danger' }}">{{ $rm->current_occupancy }} / {{ $rm->capacity }}</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 4px; background-color: var(--table-row-hover);">
                                    <div class="progress-bar progress-bar-animated-pro {{ $rm->is_available ? 'bg-primary' : 'bg-danger' }}" 
                                         data-progress="{{ $occupancyRate }}%" 
                                         style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Occupant names preview (Admin only visible, Section 33) -->
                        <div class="mt-2 pt-2 border-top" style="border-color: var(--border-color) !important;">
                            @if($rm->activeTenants->isNotEmpty())
                                <div class="text-truncate text-secondary" style="font-size: 0.72rem;">
                                    <i class="bi bi-people me-1 text-primary"></i>
                                    {{ $rm->activeTenants->pluck('full_name')->join(', ') }}
                                </div>
                            @else
                                <div class="text-muted fst-italic" style="font-size: 0.72rem;">
                                    <i class="bi bi-dash-circle me-1"></i> No current occupants
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-4 text-muted">
                No rooms registered yet. <a href="{{ route('admin.rooms.create') }}">Add a room</a>.
            </div>
        @endforelse
    </div>
</div>

<!-- Row 5: Recent Activities Split (Payments & Maintenance) -->
<div class="row g-4 mb-4">
    <!-- Recent Payments -->
    <div class="col-lg-6 animate-fade-up stagger-7">
        <div class="custom-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--border-color) !important;">
                <h5 class="fw-bold mb-0" style="color: var(--text-primary); font-size: 1.05rem;">
                    <i class="bi bi-credit-card me-2 text-success"></i> Recent Payments
                </h5>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-link text-decoration-none quick-action-pill" style="color: #38bdf8; font-size: 0.82rem;">
                    View All &rarr;
                </a>
            </div>

            <div class="table-responsive">
                <div class="table-scroll-hint">
                    <i class="bi bi-arrows-expand"></i> Swipe table horizontally to see all columns
                </div>
                <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                    <thead>
                        <tr class="text-secondary">
                            <th>Tenant</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $p)
                            <tr>
                                <td class="fw-semibold">
                                    <a href="{{ route('admin.payments.show', $p->id) }}" class="text-decoration-none" style="color: var(--text-primary);">
                                        {{ $p->tenant->full_name }}
                                    </a>
                                </td>
                                <td class="fw-bold text-success">₱{{ number_format($p->amount, 2) }}</td>
                                <td><span class="badge {{ $p->payment_method === 'cash' ? 'bg-secondary' : 'bg-primary' }}">{{ strtoupper($p->payment_method) }}</span></td>
                                <td>{{ $p->payment_date ? $p->payment_date->format('M d') : '-' }}</td>
                                <td>
                                    <span class="badge-pill {{ in_array($p->status, ['paid', 'verified']) ? 'badge-success' : ($p->status === 'pending' ? 'badge-warning' : 'badge-danger') }}" style="font-size: 0.68rem;">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No payments recorded recently.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Maintenance Requests -->
    <div class="col-lg-6 animate-fade-up stagger-7">
        <div class="custom-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--border-color) !important;">
                <h5 class="fw-bold mb-0" style="color: var(--text-primary); font-size: 1.05rem;">
                    <i class="bi bi-tools me-2 text-warning"></i> Recent Maintenance
                </h5>
                <a href="{{ route('admin.maintenance.index') }}" class="btn btn-sm btn-link text-decoration-none quick-action-pill" style="color: #38bdf8; font-size: 0.82rem;">
                    View All &rarr;
                </a>
            </div>

            <div class="table-responsive">
                <div class="table-scroll-hint">
                    <i class="bi bi-arrows-expand"></i> Swipe table horizontally to see all columns
                </div>
                <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                    <thead>
                        <tr class="text-secondary">
                            <th>Tenant</th>
                            <th>Issue</th>
                            <th>Priority</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentMaintenance as $m)
                            <tr>
                                <td class="fw-semibold">
                                    <a href="{{ route('admin.maintenance.show', $m->id) }}" class="text-decoration-none" style="color: var(--text-primary);">
                                        {{ $m->tenant->full_name }}
                                    </a>
                                    <div class="text-secondary" style="font-size: 0.72rem;">Room {{ $m->room ? $m->room->room_number : '-' }}</div>
                                </td>
                                <td class="text-truncate" style="max-width: 160px;">{{ $m->title }}</td>
                                <td>
                                    <span class="badge-pill {{ $m->priority === 'High' ? 'badge-danger' : ($m->priority === 'Medium' ? 'badge-warning' : 'badge-secondary') }}" style="font-size: 0.68rem;">
                                        {{ $m->priority }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-pill {{ $m->status === 'Resolved' ? 'badge-success' : ($m->status === 'Pending' ? 'badge-warning' : 'badge-info') }}" style="font-size: 0.68rem;">
                                        {{ $m->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No maintenance requests reported.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- CALENDAR EVENT DETAILS MODAL (Section 41 & 44) -->
<div class="modal fade" id="calendarEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-card p-4 border-0 shadow">
            <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3" style="border-color: var(--border-color) !important;">
                <div>
                    <h5 class="fw-bold mb-1" id="calModalTenantName">Tenant Rent Event</h5>
                    <div class="text-secondary" id="calModalRoomInfo" style="font-size: 0.85rem;">Room Information</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="p-3 rounded mb-3" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.88rem;">
                <div class="row g-2">
                    <div class="col-5 text-secondary">Billing Period:</div>
                    <div class="col-7 fw-semibold" id="calModalBillingPeriod">-</div>

                    <div class="col-5 text-secondary">Due Date:</div>
                    <div class="col-7 fw-bold" id="calModalDueDate">-</div>

                    <div class="col-5 text-secondary">Monthly Rent:</div>
                    <div class="col-7 fw-bold text-success" id="calModalRent">₱0.00</div>

                    <div class="col-5 text-secondary">Amount Paid:</div>
                    <div class="col-7 fw-bold" id="calModalPaid">₱0.00</div>

                    <div class="col-5 text-secondary">Remaining Balance:</div>
                    <div class="col-7 fw-bold" id="calModalBalance">₱0.00</div>

                    <div class="col-5 text-secondary">Rent Status:</div>
                    <div class="col-7" id="calModalStatusBadge">-</div>

                    <div class="col-5 text-secondary" id="calModalMethodLabel">Method:</div>
                    <div class="col-7" id="calModalMethodValue">-</div>
                </div>
            </div>

            <!-- Receipt Link if GCash -->
            <div id="calModalReceiptSection" class="mb-3 d-none">
                <a href="#" id="calModalReceiptLink" target="_blank" class="btn btn-sm btn-outline-primary w-100" style="border-radius: 8px;">
                    <i class="bi bi-file-earmark-image"></i> View GCash Receipt Proof
                </a>
            </div>

            <!-- Action Buttons based on status -->
            <div class="d-flex flex-column gap-2 mt-2" id="calModalActions">
                <!-- Dynamically populated -->
            </div>
        </div>
    </div>
</div>

<!-- RECORD PAYMENT CONFIRMATION MODAL -->
<div class="modal fade" id="recordCashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-card p-4 border-0 shadow">
            <div class="border-bottom pb-3 mb-3" style="border-color: var(--border-color) !important;">
                <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Record Rent Payment (Mark as Paid)</h5>
                <p class="text-secondary mb-0" style="font-size: 0.85rem;">Confirms payment received by Admin. Creates an official MySQL payment row.</p>
            </div>

            <!-- Error Alert in Modal -->
            <div id="paymentModalErrorAlert" class="alert alert-danger py-2 px-3 mb-3 {{ $errors->any() ? '' : 'd-none' }}" style="border-radius: 8px;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                    <strong>Please fix the errors below:</strong>
                </div>
                <ul class="mb-0 ps-3" id="paymentModalErrorList" style="font-size: 0.85rem;">
                    @if($errors->any())
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @else
                        <li>The payment method field is required.</li>
                    @endif
                </ul>
            </div>

            <form id="recordPaymentForm" action="{{ route('admin.payments.record-cash-quick') }}" method="POST" onsubmit="return validateRecordPaymentForm(event)">
                @csrf
                <input type="hidden" name="tenant_id" id="cashTenantId">
                <input type="hidden" name="billing_month" id="cashBillingMonth">
                <input type="hidden" name="billing_year" id="cashBillingYear">

                <div class="p-3 rounded mb-3" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.88rem;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Tenant:</span>
                        <strong id="cashTenantNameDisplay">-</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Room:</span>
                        <span id="cashRoomDisplay">-</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Payment For:</span>
                        <strong id="cashPeriodDisplay">-</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Monthly Rent:</span>
                        <span id="cashRentDisplay">₱0.00</span>
                    </div>
                </div>

                <!-- Payment Method (Required) -->
                <div class="mb-3">
                    <label for="cashPaymentMethod" class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select name="payment_method" id="cashPaymentMethod" class="form-select @error('payment_method') is-invalid @enderror" onchange="document.getElementById('paymentModalErrorAlert').classList.add('d-none')">
                        <option value="">-- Select Payment Method --</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="gcash" {{ old('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                    </select>
                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="cashAmount" class="form-label">Amount Received (₱) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">₱</span>
                        <input type="number" step="0.01" name="amount" id="cashAmount" class="form-control border-start-0 ps-0" required>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label for="cashDate" class="form-label">Date Received <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="cashDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="cashTime" class="form-label">Time Received</label>
                        <input type="time" name="payment_time" id="cashTime" class="form-control" value="{{ date('H:i') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="cashRemarks" class="form-label">Remarks (Optional)</label>
                    <input type="text" name="remarks" id="cashRemarks" class="form-control" value="Payment received directly by Admin">
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary-custom">
                        <i class="bi bi-check-circle"></i> Confirm Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCalendarEventModal(eventData, month, year) {
        document.getElementById('calModalTenantName').textContent = eventData.tenant_name;
        document.getElementById('calModalRoomInfo').textContent = 'Room ' + eventData.room_number + ' (' + eventData.room_type + ')';
        
        const monthNames = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        document.getElementById('calModalBillingPeriod').textContent = monthNames[month] + ' ' + year;
        document.getElementById('calModalDueDate').textContent = eventData.due_date;
        document.getElementById('calModalRent').textContent = '₱' + parseFloat(eventData.monthly_rent).toLocaleString('en-US', {minimumFractionDigits: 2});
        const paidElem = document.getElementById('calModalPaid');
        if (eventData.status === 'partial') {
            paidElem.innerHTML = '₱' + parseFloat(eventData.paid_amount).toLocaleString('en-US', {minimumFractionDigits: 2}) + ' <span class="badge badge-partial ms-1" style="font-size: 0.72rem;"><i class="bi bi-pie-chart-fill me-1"></i>Partially Paid</span>';
        } else if (eventData.status === 'paid') {
            paidElem.innerHTML = '₱' + parseFloat(eventData.paid_amount).toLocaleString('en-US', {minimumFractionDigits: 2}) + ' <span class="badge bg-success ms-1" style="font-size: 0.72rem;"><i class="bi bi-check-circle-fill me-1"></i>Fully Paid</span>';
        } else if (eventData.status === 'pending_verification') {
            paidElem.innerHTML = '₱' + parseFloat(eventData.paid_amount).toLocaleString('en-US', {minimumFractionDigits: 2}) + ' <span class="badge badge-warning ms-1" style="font-size: 0.72rem;"><i class="bi bi-hourglass-split me-1"></i>Waiting for Approval</span>';
        } else {
            paidElem.textContent = '₱' + parseFloat(eventData.paid_amount).toLocaleString('en-US', {minimumFractionDigits: 2});
        }
        
        const balElem = document.getElementById('calModalBalance');
        balElem.textContent = '₱' + parseFloat(eventData.balance).toLocaleString('en-US', {minimumFractionDigits: 2});
        balElem.className = eventData.balance > 0 ? 'col-7 fw-bold text-danger' : 'col-7 fw-bold text-success';

        let badgeHtml = '';
        if (eventData.status === 'paid') {
            badgeHtml = '<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Fully Paid</span>';
        } else if (eventData.status === 'pending_verification') {
            badgeHtml = '<span class="badge badge-warning"><i class="bi bi-hourglass-split me-1"></i>Waiting for Approval</span>';
        } else if (eventData.status === 'partial') {
            badgeHtml = '<span class="badge badge-partial"><i class="bi bi-pie-chart-fill me-1"></i>Partially Paid</span>';
        } else if (eventData.status === 'overdue' || eventData.status === 'due') {
            badgeHtml = '<span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>' + eventData.label + '</span>';
        } else {
            badgeHtml = '<span class="badge bg-secondary">' + eventData.label + '</span>';
        }
        document.getElementById('calModalStatusBadge').innerHTML = badgeHtml;

        document.getElementById('calModalMethodValue').textContent = eventData.payment_method ? eventData.payment_method.toUpperCase() : 'None yet';

        // Receipt section
        const receiptSec = document.getElementById('calModalReceiptSection');
        if (eventData.receipt_url) {
            receiptSec.classList.remove('d-none');
            document.getElementById('calModalReceiptLink').href = eventData.receipt_url;
        } else {
            receiptSec.classList.add('d-none');
        }

        // Action buttons
        const actionsDiv = document.getElementById('calModalActions');
        actionsDiv.innerHTML = '';

        if (eventData.balance > 0 && eventData.status !== 'pending_verification') {
            const markPaidBtn = document.createElement('button');
            markPaidBtn.type = 'button';
            markPaidBtn.className = 'btn-primary-custom justify-content-center py-2';
            if (eventData.status === 'partial') {
                markPaidBtn.innerHTML = '<i class="bi bi-cash-stack"></i> Settle Balance (₱' + parseFloat(eventData.balance).toLocaleString('en-US', {minimumFractionDigits: 2}) + ')';
            } else {
                markPaidBtn.innerHTML = '<i class="bi bi-cash-stack"></i> Mark as Paid';
            }
            markPaidBtn.onclick = function() {
                const calModal = bootstrap.Modal.getInstance(document.getElementById('calendarEventModal'));
                if (calModal) calModal.hide();

                openRecordCashModal(eventData, month, year);
            };
            actionsDiv.appendChild(markPaidBtn);
        }

        if (eventData.payment_id) {
            const viewPaymentBtn = document.createElement('a');
            viewPaymentBtn.href = '/admin/payments/' + eventData.payment_id;
            viewPaymentBtn.className = 'btn-secondary-custom justify-content-center py-2 text-decoration-none';
            viewPaymentBtn.innerHTML = '<i class="bi bi-eye"></i> View Payment Record';
            actionsDiv.appendChild(viewPaymentBtn);
        }

        const viewTenantBtn = document.createElement('a');
        viewTenantBtn.href = '/admin/tenants/' + eventData.tenant_id;
        viewTenantBtn.className = 'btn btn-link text-decoration-none text-center py-1';
        viewTenantBtn.style.fontSize = '0.85rem';
        viewTenantBtn.innerHTML = '<i class="bi bi-person"></i> View Tenant Profile';
        actionsDiv.appendChild(viewTenantBtn);

        const modal = new bootstrap.Modal(document.getElementById('calendarEventModal'));
        modal.show();
    }

    function openRecordCashModal(eventData, month, year) {
        document.getElementById('cashTenantId').value = eventData.tenant_id || '';
        document.getElementById('cashBillingMonth').value = month;
        document.getElementById('cashBillingYear').value = year;

        const monthNames = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        document.getElementById('cashTenantNameDisplay').textContent = eventData.tenant_name || '-';
        document.getElementById('cashRoomDisplay').textContent = eventData.room_number ? ('Room ' + eventData.room_number) : '-';
        document.getElementById('cashPeriodDisplay').textContent = monthNames[month] + ' ' + year;
        document.getElementById('cashRentDisplay').textContent = '₱' + parseFloat(eventData.monthly_rent || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('cashAmount').value = eventData.balance || '';

        const errorAlert = document.getElementById('paymentModalErrorAlert');
        if (errorAlert) {
            errorAlert.classList.add('d-none');
        }
        const methodSelect = document.getElementById('cashPaymentMethod');
        if (methodSelect) {
            methodSelect.classList.remove('is-invalid');
            methodSelect.value = '';
        }

        const modal = new bootstrap.Modal(document.getElementById('recordCashModal'));
        modal.show();
    }

    function validateRecordPaymentForm(event) {
        const methodSelect = document.getElementById('cashPaymentMethod');
        const alertBox = document.getElementById('paymentModalErrorAlert');
        const errorList = document.getElementById('paymentModalErrorList');

        if (!methodSelect || !methodSelect.value.trim()) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            if (methodSelect) {
                methodSelect.classList.add('is-invalid');
                methodSelect.focus();
            }
            if (errorList) {
                errorList.innerHTML = '<li>The payment method field is required.</li>';
            }
            if (alertBox) {
                alertBox.classList.remove('d-none');
            }
            return false;
        }

        if (methodSelect) {
            methodSelect.classList.remove('is-invalid');
        }
        if (alertBox) {
            alertBox.classList.add('d-none');
        }
        return true;
    }

    @if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        const recordModalEl = document.getElementById('recordCashModal');
        if (recordModalEl) {
            const modal = new bootstrap.Modal(recordModalEl);
            modal.show();
        }
    });
    @endif
</script>
@endsection
