@php
    $pendingCount = \App\Models\Tenant::whereHas('user', function ($q) {
        $q->where('role', 'tenant')->where('account_status', 'pending');
    })->count();
    $pendingPaymentCount = \App\Models\Payment::where('status', 'pending')->count();
    $pendingMaintenanceCount = \App\Models\MaintenanceRequest::whereIn('status', ['pending', 'Pending'])->count();
    $pendingRoomRequestCount = \App\Models\RoomRequest::where('status', 'pending')->count();
@endphp

<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon logo-animated">
            <i class="bi bi-houses-fill"></i>
        </div>
        <div class="sidebar-brand-text">
            Integrated Boarding House<br><span style="font-weight: 500; font-size: 0.72rem; color: var(--text-secondary); text-transform: none; letter-spacing: 0;">Management System</span>
        </div>
        <button type="button" class="btn btn-link text-secondary p-0 d-lg-none ms-auto sidebar-close-btn" aria-label="Close Navigation" style="font-size: 1.25rem; line-height: 1;">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill fs-5"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.pending-tenants.index') }}" class="sidebar-link {{ request()->routeIs('admin.pending-tenants.*') ? 'active' : '' }}">
            <i class="bi bi-person-plus-fill fs-5"></i>
            <span class="flex-grow-1">Pending Accounts</span>
            @if($pendingCount > 0)
                <span class="badge rounded-pill bg-danger" style="font-size: 0.7rem;">{{ $pendingCount }}</span>
            @endif
        </a>

        <a href="{{ route('admin.tenants.index') }}" class="sidebar-link {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill fs-5"></i>
            <span>Tenants</span>
        </a>

        <a href="{{ route('admin.rooms.index') }}" class="sidebar-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
            <i class="bi bi-door-open-fill fs-5"></i>
            <span>Rooms</span>
        </a>

        <a href="{{ route('admin.room-requests.index') }}" class="sidebar-link {{ request()->routeIs('admin.room-requests.*') ? 'active' : '' }}">
            <i class="bi bi-bookmark-check-fill fs-5"></i>
            <span class="flex-grow-1">Room Requests</span>
            @if($pendingRoomRequestCount > 0)
                <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.7rem;">{{ $pendingRoomRequestCount }}</span>
            @endif
        </a>

        <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card-2-front-fill fs-5"></i>
            <span class="flex-grow-1">Payments</span>
            @if($pendingPaymentCount > 0)
                <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.7rem;">{{ $pendingPaymentCount }}</span>
            @endif
        </a>

        <a href="{{ route('admin.maintenance.index') }}" class="sidebar-link {{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}">
            <i class="bi bi-tools fs-5"></i>
            <span class="flex-grow-1">Maintenance</span>
            @if($pendingMaintenanceCount > 0)
                <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.7rem;">{{ $pendingMaintenanceCount }}</span>
            @endif
        </a>

        <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line-fill fs-5"></i>
            <span>Reports</span>
        </a>

        <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear-fill fs-5"></i>
            <span>Settings</span>
        </a>
    </div>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-danger">
                <i class="bi bi-box-arrow-left fs-5"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
