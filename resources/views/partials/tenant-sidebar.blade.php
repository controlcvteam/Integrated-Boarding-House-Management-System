<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon logo-animated">
            <i class="bi bi-houses-fill"></i>
        </div>
        <div class="sidebar-brand-text">
            Integrated Boarding House<br><span style="font-weight: 500; font-size: 0.72rem; color: var(--text-secondary); text-transform: none; letter-spacing: 0;">Tenant Portal</span>
        </div>
        <button type="button" class="btn btn-link text-secondary p-0 d-lg-none ms-auto sidebar-close-btn" aria-label="Close Navigation" style="font-size: 1.25rem; line-height: 1;">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar-nav">
        <a href="{{ route('tenant.dashboard') }}" class="sidebar-link {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill fs-5"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('tenant.rooms.index') }}" class="sidebar-link {{ request()->routeIs('tenant.rooms.*') ? 'active' : '' }}">
            <i class="bi bi-door-open-fill fs-5"></i>
            <span>Available Rooms</span>
        </a>

        <a href="{{ route('tenant.my-room') }}" class="sidebar-link {{ request()->routeIs('tenant.my-room') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill fs-5"></i>
            <span>My Room</span>
        </a>

        <a href="{{ route('tenant.payments.index') }}" class="sidebar-link {{ request()->routeIs('tenant.payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card-2-front-fill fs-5"></i>
            <span>Payments</span>
        </a>

        <a href="{{ route('tenant.maintenance.index') }}" class="sidebar-link {{ request()->routeIs('tenant.maintenance.*') ? 'active' : '' }}">
            <i class="bi bi-tools fs-5"></i>
            <span>Maintenance</span>
        </a>

        <a href="{{ route('tenant.settings.index') }}" class="sidebar-link {{ request()->routeIs('tenant.settings.*') ? 'active' : '' }}">
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
