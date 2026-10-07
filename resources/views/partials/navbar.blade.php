@php
    $currentUser = auth()->user();
    $notifications = $currentUser ? $currentUser->notifications()->take(15)->get() : collect();
    $unreadCount = $currentUser ? $currentUser->unreadNotifications()->count() : 0;
@endphp

<header class="app-navbar">
    <div class="navbar-title-wrap">
        <button type="button" class="btn btn-link text-secondary p-0 d-lg-none me-2" id="sidebarToggleBtn" aria-label="Toggle Navigation">
            <i class="bi bi-list fs-2"></i>
        </button>
        <div class="navbar-title-text-box overflow-hidden">
            <h1 class="navbar-page-title text-truncate">@yield('page_title', 'Dashboard')</h1>
            <p class="navbar-page-subtitle d-none d-sm-block text-truncate">@yield('page_subtitle', 'Overview and management')</p>
        </div>
    </div>

    <div class="navbar-actions">
        <!-- Live Real-Time Clock Display near Theme Toggle -->
        <div class="navbar-clock-pill d-none d-md-flex align-items-center gap-2 px-3 py-1 me-1 border rounded-pill shadow-xs" 
             style="background: var(--bg-card); border-color: var(--border-color) !important; font-size: 0.8rem;" 
             title="Current System Time">
            <i class="bi bi-clock text-secondary" style="font-size: 0.85rem;"></i>
            <div class="d-flex flex-column text-end" style="line-height: 1.15;">
                <span class="fw-bold font-monospace" id="navLiveClockTime" style="color: var(--text-primary); font-size: 0.82rem; letter-spacing: 0.5px;">{{ now()->format('h:i:s A') }}</span>
                <span class="text-secondary" id="navLiveClockDate" style="font-size: 0.65rem;">{{ now()->format('D, M d, Y') }}</span>
            </div>
        </div>

        <!-- Dark / Light Mode Toggle Button -->
        <button type="button" class="theme-toggle-btn" title="Toggle Theme" aria-label="Toggle Theme">
            <i class="bi bi-sun-fill theme-sun-icon" style="display: none; color: #f59e0b;"></i>
            <i class="bi bi-moon-fill theme-moon-icon" style="color: #64748b;"></i>
        </button>

        <!-- Notification Bell Dropdown -->
        <div class="position-relative">
            <button type="button" class="notif-bell-btn" id="notifBellBtn" title="Notifications" aria-label="Notifications">
                <i class="bi bi-bell-fill"></i>
                @if($unreadCount > 0)
                    <span class="notif-badge">{{ $unreadCount }}</span>
                @endif
            </button>

            <div class="notif-dropdown d-none shadow-lg custom-card p-0" id="notifDropdown" style="position: absolute; right: 0; top: 48px; width: min(350px, calc(100vw - 24px)); z-index: 1050; border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color);">
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom" style="background: var(--table-header-bg);">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold" style="font-size: 0.9rem; color: var(--text-primary);">Notifications</span>
                        @if($unreadCount > 0)
                            <span class="badge bg-primary text-white rounded-pill px-2 py-0" style="font-size: 0.68rem;">{{ $unreadCount }} new</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($unreadCount > 0)
                            <form action="{{ route('notifications.read-all') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 text-decoration-none fw-semibold" style="font-size: 0.74rem; color: #0284c7;">Mark all read</button>
                            </form>
                        @endif
                        @if($notifications->isNotEmpty())
                            <form action="{{ route('notifications.destroy-all') }}" method="POST" class="m-0" onsubmit="return confirm('Delete all notifications?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link p-0 text-decoration-none text-danger fw-semibold" style="font-size: 0.74rem;">
                                    <i class="bi bi-trash3 me-1"></i>Delete all
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div style="max-height: 320px; overflow-y: auto;">
                    @forelse($notifications as $notif)
                        @php
                            $fullText = strtolower($notif->title . ' ' . $notif->message);
                            $isGcash = \Illuminate\Support\Str::contains($fullText, 'gcash');
                            $isCash = !$isGcash && \Illuminate\Support\Str::contains($fullText, 'cash');
                            
                            $isSuccess = $notif->type === 'success' 
                                || \Illuminate\Support\Str::contains(strtolower($notif->title), ['[success]', 'verified', 'approved', 'successfully paid', 'successfully recorded']);
                            $isFailed = in_array($notif->type, ['danger', 'error']) 
                                || \Illuminate\Support\Str::contains($fullText, ['[failed]', 'rejected', 'failed', 'could not be verified', 'overdue']);
                            $isPending = !$isSuccess && !$isFailed && \Illuminate\Support\Str::contains($fullText, ['[pending]', 'pending', 'awaiting']);
                            
                            $displayTitle = trim(str_ireplace(['[SUCCESS]', '[FAILED]', '[PENDING]'], '', $notif->title));
                        @endphp
                        <div class="d-flex align-items-start justify-content-between p-3 notif-item-pro gap-2 {{ $notif->is_read ? '' : 'unread' }}" 
                             style="background: {{ $notif->is_read ? 'var(--bg-card)' : 'rgba(2, 132, 199, 0.05)' }}; transition: background 0.15s ease;">
                            <div class="d-flex align-items-start gap-2 flex-grow-1 overflow-hidden">
                                @if(!$notif->is_read)
                                    <span class="badge rounded-pill bg-primary mt-1 flex-shrink-0" style="width: 8px; height: 8px; padding: 0;" title="Unread">•</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary bg-opacity-25 mt-1 flex-shrink-0" style="width: 8px; height: 8px; padding: 0;" title="Read"></span>
                                @endif
                                <div class="overflow-hidden">
                                    <a href="{{ $notif->link ?? '#' }}" class="text-decoration-none text-reset d-block">
                                        <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                                            @if($isSuccess)
                                                <span class="notif-pill notif-pill-success">
                                                    <i class="bi bi-check-circle-fill"></i>SUCCESS
                                                </span>
                                            @elseif($isFailed)
                                                <span class="notif-pill notif-pill-failed">
                                                    <i class="bi bi-x-circle-fill"></i>FAILED
                                                </span>
                                            @elseif($isPending)
                                                <span class="notif-pill notif-pill-pending">
                                                    <i class="bi bi-hourglass-split"></i>PENDING
                                                </span>
                                            @endif

                                            @if($isGcash)
                                                <span class="notif-pill notif-pill-gcash">
                                                    <i class="bi bi-wallet2"></i>GCASH
                                                </span>
                                            @elseif($isCash)
                                                <span class="notif-pill notif-pill-cash">
                                                    <i class="bi bi-cash-stack"></i>CASH
                                                </span>
                                            @endif
                                        </div>
                                        <div class="fw-semibold text-truncate {{ $notif->is_read ? 'text-secondary' : 'text-primary-emphasis' }}" style="font-size: 0.82rem; color: var(--text-primary);">
                                            {{ $displayTitle }}
                                        </div>
                                        <div class="text-secondary" style="font-size: 0.76rem; line-height: 1.3;">{{ $notif->message }}</div>
                                        <div class="text-muted mt-1" style="font-size: 0.68rem;"><i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}</div>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Action buttons: Mark Read/Unread & Delete Single -->
                            <div class="d-flex align-items-center gap-1 flex-shrink-0 ms-1 pt-1">
                                @if($notif->is_read)
                                    <form action="{{ route('notifications.unread', $notif->id) }}" method="POST" class="m-0" title="Mark as unread">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link p-1 text-secondary" style="font-size: 0.8rem; line-height: 1;">
                                            <i class="bi bi-envelope"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('notifications.read-single', $notif->id) }}" method="POST" class="m-0" title="Mark as read">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link p-1 text-primary" style="font-size: 0.8rem; line-height: 1;">
                                            <i class="bi bi-envelope-open"></i>
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this notification?')" title="Delete this notification">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link p-1 text-danger opacity-75" style="font-size: 0.8rem; line-height: 1;">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted" style="font-size: 0.82rem;">
                            <i class="bi bi-bell-slash fs-3 d-block mb-2 text-secondary opacity-50"></i>
                            No notifications yet
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <button class="navbar-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="navbar-user-avatar overflow-hidden border">
                    @if($currentUser && $currentUser->profile_picture_url)
                        <img src="{{ $currentUser->profile_picture_url }}" alt="{{ $currentUser->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="bi bi-person-fill"></i>
                    @endif
                </div>
                <div class="navbar-user-info">
                    <div class="navbar-user-name">{{ $currentUser->name ?? 'User' }}</div>
                    <div class="navbar-user-role">
                        @if($currentUser && $currentUser->isAdmin())
                            Admin
                        @else
                            Tenant
                        @endif
                    </div>
                </div>
                <i class="bi bi-chevron-down ms-1 text-secondary" style="font-size: 0.75rem;"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="background-color: var(--bg-card); border-color: var(--border-color); border-radius: 8px;">
                <li>
                    <h6 class="dropdown-header text-secondary" style="font-size: 0.75rem;">Signed in as</h6>
                    <div class="px-3 py-1 fw-bold text-truncate" style="font-size: 0.85rem; color: var(--text-primary); max-width: 200px;">{{ $currentUser->email ?? '' }}</div>
                </li>
                <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                <li>
                    @if($currentUser && $currentUser->isAdmin())
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.settings.index') }}" style="color: var(--text-primary); font-size: 0.88rem;">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    @else
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('tenant.settings.index') }}" style="color: var(--text-primary); font-size: 0.88rem;">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    @endif
                </li>
                <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" style="font-size: 0.88rem;">
                            <i class="bi bi-box-arrow-left"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
