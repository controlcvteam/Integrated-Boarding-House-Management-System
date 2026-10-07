// Integrated Boarding House Management System - Professional Vanilla JavaScript & Animations

document.addEventListener('DOMContentLoaded', () => {
    // 1. Dark Mode / Light Mode Management
    initTheme();

    // 2. Mobile Sidebar Toggle & Overlay
    initSidebar();

    // 3. Modals and Triggers
    initModals();

    // 4. Notifications Bell Toggle
    initNotifications();

    // 5. High-Performance Page Entrance & Micro-Animations
    initAnimations();

    // 6. Flash Alert Auto-Dismiss
    initAlerts();

    // 7. Live Real-Time Digital Clock
    initLiveClock();
});

function initTheme() {
    const savedTheme = localStorage.getItem('ibms_theme') || 
        (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    applyTheme(savedTheme);

    const toggleBtns = document.querySelectorAll('.theme-toggle-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
            localStorage.setItem('ibms_theme', newTheme);
        });
    });
}

function applyTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        updateThemeIcons(true);
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.setAttribute('data-bs-theme', 'light');
        updateThemeIcons(false);
    }
}

function updateThemeIcons(isDark) {
    const sunIcons = document.querySelectorAll('.theme-sun-icon');
    const moonIcons = document.querySelectorAll('.theme-moon-icon');

    sunIcons.forEach(el => el.style.display = isDark ? 'inline-block' : 'none');
    moonIcons.forEach(el => el.style.display = isDark ? 'none' : 'inline-block');
}

function initSidebar() {
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebar = document.getElementById('appSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const closeBtns = document.querySelectorAll('.sidebar-close-btn, #sidebarCloseBtn');

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('show-mobile');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    function openSidebar() {
        if (sidebar) sidebar.classList.add('show-mobile');
        if (backdrop) backdrop.classList.add('show');
        if (window.innerWidth < 992) {
            document.body.style.overflow = 'hidden';
        }
    }

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (sidebar.classList.contains('show-mobile')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    closeBtns.forEach(btn => {
        btn.addEventListener('click', closeSidebar);
    });

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    // Close on navigation link click when on mobile/tablet viewports
    if (sidebar) {
        sidebar.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });

        // Touch Swipe-to-close on mobile/tablet
        let touchStartX = 0;
        let touchStartY = 0;

        sidebar.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        sidebar.addEventListener('touchend', (e) => {
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            const diffX = touchEndX - touchStartX;
            const diffY = touchEndY - touchStartY;

            // Horizontal swipe to the left by at least 50px with less vertical deviation
            if (diffX < -50 && Math.abs(diffY) < 100) {
                closeSidebar();
            }
        }, { passive: true });
    }

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show-mobile')) {
            closeSidebar();
        }
    });

    // Auto-restore scroll if window is resized past 992px
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });
}

function initModals() {
    // Automatic modal teleport to body to prevent stacking context or transform trapping behind backdrop
    document.addEventListener('show.bs.modal', (e) => {
        const modal = e.target;
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    // Generic modal open helper via data-modal-target
    document.querySelectorAll('[data-modal-target]').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = trigger.getAttribute('data-modal-target');
            const modal = document.getElementById(targetId);
            if (modal) {
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
                modal.classList.add('show');
                modal.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        });
    });

    // Close buttons inside modals
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal-container') || btn.closest('.modal');
            if (modal) {
                modal.classList.remove('show');
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        });
    });
}

function initNotifications() {
    const notifBtn = document.getElementById('notifBellBtn');
    const notifDropdown = document.getElementById('notifDropdown');

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.classList.toggle('d-none');
        });

        document.addEventListener('click', (e) => {
            if (!notifDropdown.contains(e.target) && e.target !== notifBtn) {
                notifDropdown.classList.add('d-none');
            }
        });
    }
}

function initAnimations() {
    const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) return;

    // Apply staggered entrance animation to page cards and sections
    const contentCards = document.querySelectorAll('.app-content > .custom-card, .app-content > .row, .auth-card, .hero-welcome-card, .rent-alert-banner');
    contentCards.forEach((el, index) => {
        el.classList.add('animate-fade-up');
        const delay = Math.min((index + 1) * 0.05, 0.35);
        el.style.animationDelay = `${delay}s`;
    });

    // Enhance status badges with live pulsing dots
    enhanceStatusBadges();

    // Smooth KPI Number Counters
    initCounters();

    // Animated Progress Bars
    initProgressBars();
}

function initCounters() {
    const counterElements = document.querySelectorAll('[data-counter], [data-counter-currency]');
    counterElements.forEach(el => {
        const isCurrency = el.hasAttribute('data-counter-currency');
        const targetValue = parseFloat(el.getAttribute('data-counter') || el.getAttribute('data-counter-currency'));
        if (isNaN(targetValue)) return;

        const duration = 1200; // ms
        const startTime = performance.now();

        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // Ease out cubic
            const easeProgress = 1 - Math.pow(1 - progress, 3);
            const currentValue = targetValue * easeProgress;

            if (isCurrency) {
                el.textContent = '₱' + currentValue.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                el.textContent = Math.round(currentValue).toLocaleString('en-US');
            }

            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                if (isCurrency) {
                    el.textContent = '₱' + targetValue.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                } else {
                    el.textContent = Math.round(targetValue).toLocaleString('en-US');
                }
            }
        }

        requestAnimationFrame(updateCounter);
    });
}

function initProgressBars() {
    const progressBars = document.querySelectorAll('[data-progress]');
    progressBars.forEach(bar => {
        const targetWidth = bar.getAttribute('data-progress');
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 150);
    });
}

function enhanceStatusBadges() {
    const badges = document.querySelectorAll('.badge-pill, .badge');
    badges.forEach(badge => {
        // Skip if already contains a badge-dot
        if (badge.querySelector('.badge-dot')) return;

        const text = badge.textContent.trim().toLowerCase();

        // STRICT: Never put glowing green circle on 'not available', 'unavailable', 'occupied', or 'full'
        if (text.includes('not available') || text.includes('unavailable') || text.includes('not-available') || text.includes('occupied') || text.includes('full')) {
            return;
        }

        // Also do not add green dots in room cards/sections
        if (badge.closest('.room-card, .room-overview-card, .room-section, #rooms-table, [data-section="rooms"], .rooms-grid') && text.includes('available')) {
            return;
        }

        let dotClass = '';

        if (text.includes('paid') || text.includes('active') || text.includes('completed') || text.includes('resolved') || text.includes('approved')) {
            dotClass = 'pulse-success';
        } else if (text === 'available' || (text.includes('available') && !text.includes('not available') && !text.includes('unavailable'))) {
            dotClass = 'pulse-success';
        } else if (text.includes('pending') || text.includes('upcoming') || text.includes('due soon') || text.includes('partial')) {
            dotClass = 'pulse-warning';
        } else if (text.includes('overdue') || text.includes('rejected') || text.includes('maintenance')) {
            dotClass = 'pulse-danger';
        } else if (text.includes('in progress') || text.includes('scheduled')) {
            dotClass = 'pulse-info';
        }

        if (dotClass) {
            const dot = document.createElement('span');
            dot.className = `badge-dot ${dotClass}`;
            badge.prepend(dot);
        }
    });
}

function initAlerts() {
    // Auto-dismiss floating popup toasts and alerts smoothly after 5 seconds
    const popups = document.querySelectorAll('.toast-popup, .alert:not(.alert-permanent):not(.rent-alert-banner):not(#paymentModalErrorAlert)');
    popups.forEach(popup => {
        setTimeout(() => {
            popup.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            popup.style.opacity = '0';
            popup.style.transform = 'translateY(-14px)';
            setTimeout(() => {
                if (popup.parentNode) {
                    popup.remove();
                }
            }, 400);
        }, 5000);
    });
}

// Global modal helper
window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }
};

window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
};

function initLiveClock() {
    const timeEl = document.getElementById('navLiveClockTime');
    const dateEl = document.getElementById('navLiveClockDate');
    if (!timeEl && !dateEl) return;

    function updateClock() {
        const now = new Date();
        if (timeEl) {
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? String(hours).padStart(2, '0') : '12';
            timeEl.textContent = `${hours}:${minutes}:${seconds} ${ampm}`;
        }
        if (dateEl) {
            const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
            dateEl.textContent = now.toLocaleDateString('en-US', options);
        }
    }

    updateClock();
    setInterval(updateClock, 1000);
}
