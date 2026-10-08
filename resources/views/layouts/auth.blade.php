<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication') - Integrated Boarding House Management System</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.svg') }}">

    <script>
        (function() {
            const theme = localStorage.getItem('ibms_theme') || 
                (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-bs-theme', 'light');
            }
        })();
    </script>

    <!-- Preconnect & DNS-Prefetch for CDNs -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
        }
        .auth-header-toggle {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
        }
        .auth-card {
            width: 100%;
            max-width: @yield('card_width', '450px');
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 2rem 2.25rem;
        }
        @media (max-width: 576px) {
            .auth-card {
                padding: 1.5rem 1.25rem;
            }
        }
        .auth-logo-badge {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: #0f172a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1.25rem;
        }
        html.dark .auth-logo-badge {
            background: #38bdf8;
            color: #0f172a;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-container">
        <div class="auth-header-toggle">
            <button type="button" class="theme-toggle-btn" title="Toggle Theme" aria-label="Toggle Theme">
                <i class="bi bi-sun-fill theme-sun-icon" style="display: none; color: #f59e0b;"></i>
                <i class="bi bi-moon-fill theme-moon-icon" style="color: #64748b;"></i>
            </button>
        </div>

        <div class="auth-card">
            <div class="text-center mb-4">
                <div class="auth-logo-badge logo-animated">
                    <i class="bi bi-houses-fill"></i>
                </div>
                <h2 class="fw-bold mb-1" style="font-size: 1.35rem; color: var(--text-primary);">Integrated Boarding House</h2>
                <p class="text-secondary" style="font-size: 0.85rem;">Management System</p>
            </div>

            @include('partials.alerts')
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS & App JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
