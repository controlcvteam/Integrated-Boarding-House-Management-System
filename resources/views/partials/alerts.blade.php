<div id="popupToastNotificationContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999; max-width: 440px; width: calc(100% - 2rem); pointer-events: none;">
    @if(session('success'))
        @php
            $msg = session('success');
            $isGcash = \Illuminate\Support\Str::contains(strtolower($msg), 'gcash');
            $isCash = !$isGcash && \Illuminate\Support\Str::contains(strtolower($msg), 'cash');
            $cleanMsg = str_ireplace(['[SUCCESS]', '[SUCCESS!]', '[SUCCESS: ]'], '', $msg);
        @endphp
        <div class="toast-popup toast-popup-pro toast-success p-3 mb-3 d-flex align-items-start gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 38px; height: 38px; background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 1.15rem; border: 1px solid rgba(16, 185, 129, 0.25);">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    @if($isGcash)
                        <span class="badge" style="background: #0284c7; color: #fff; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; border-radius: 6px; padding: 2px 7px;">
                            <i class="bi bi-wallet2 me-1"></i>GCASH
                        </span>
                    @elseif($isCash)
                        <span class="badge" style="background: #0f172a; color: #fff; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; border-radius: 6px; padding: 2px 7px;">
                            <i class="bi bi-cash-stack me-1"></i>CASH
                        </span>
                    @endif
                    <span class="fw-bold" style="font-size: 0.88rem; color: #059669;">Success</span>
                </div>
                <div class="fw-medium text-break" style="font-size: 0.85rem; line-height: 1.45; color: var(--text-primary);">
                    {{ trim($cleanMsg) }}
                </div>
            </div>
            <button type="button" class="btn-close ms-1 mt-1 flex-shrink-0" style="font-size: 0.75rem;" onclick="this.closest('.toast-popup').remove()" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        @php
            $msg = session('error');
            $isGcash = \Illuminate\Support\Str::contains(strtolower($msg), 'gcash');
            $isCash = !$isGcash && \Illuminate\Support\Str::contains(strtolower($msg), 'cash');
            $cleanMsg = str_ireplace(['[FAILED]', '[FAILED!]', '[FAILED: ]', '[ERROR]'], '', $msg);
        @endphp
        <div class="toast-popup toast-popup-pro toast-error p-3 mb-3 d-flex align-items-start gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 38px; height: 38px; background: rgba(239, 68, 68, 0.12); color: #dc2626; font-size: 1.15rem; border: 1px solid rgba(239, 68, 68, 0.25);">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    @if($isGcash)
                        <span class="badge" style="background: #0284c7; color: #fff; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; border-radius: 6px; padding: 2px 7px;">
                            <i class="bi bi-wallet2 me-1"></i>GCASH
                        </span>
                    @elseif($isCash)
                        <span class="badge" style="background: #0f172a; color: #fff; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; border-radius: 6px; padding: 2px 7px;">
                            <i class="bi bi-cash-stack me-1"></i>CASH
                        </span>
                    @endif
                    <span class="fw-bold" style="font-size: 0.88rem; color: #dc2626;">Failed</span>
                </div>
                <div class="fw-medium text-break" style="font-size: 0.85rem; line-height: 1.45; color: var(--text-primary);">
                    {{ trim($cleanMsg) }}
                </div>
            </div>
            <button type="button" class="btn-close ms-1 mt-1 flex-shrink-0" style="font-size: 0.75rem;" onclick="this.closest('.toast-popup').remove()" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="toast-popup toast-popup-pro toast-info p-3 mb-3 d-flex align-items-start gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 38px; height: 38px; background: rgba(2, 132, 199, 0.12); color: #0284c7; font-size: 1.15rem; border: 1px solid rgba(2, 132, 199, 0.25);">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="fw-bold mb-1" style="font-size: 0.88rem; color: #0284c7;">Information</div>
                <div class="fw-medium text-break" style="font-size: 0.85rem; line-height: 1.45; color: var(--text-primary);">
                    {{ session('info') }}
                </div>
            </div>
            <button type="button" class="btn-close ms-1 mt-1 flex-shrink-0" style="font-size: 0.75rem;" onclick="this.closest('.toast-popup').remove()" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="toast-popup toast-popup-pro toast-warning p-3 mb-3 d-flex align-items-start gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 38px; height: 38px; background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 1.15rem; border: 1px solid rgba(245, 158, 11, 0.25);">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="fw-bold mb-1" style="font-size: 0.88rem; color: #b45309;">Notice</div>
                <div class="fw-medium text-break" style="font-size: 0.85rem; line-height: 1.45; color: var(--text-primary);">
                    {{ session('warning') }}
                </div>
            </div>
            <button type="button" class="btn-close ms-1 mt-1 flex-shrink-0" style="font-size: 0.75rem;" onclick="this.closest('.toast-popup').remove()" aria-label="Close"></button>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="toast-popup toast-popup-pro toast-error p-3 mb-3 d-flex align-items-start gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 38px; height: 38px; background: rgba(239, 68, 68, 0.12); color: #dc2626; font-size: 1.15rem; border: 1px solid rgba(239, 68, 68, 0.25);">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="fw-bold mb-1" style="font-size: 0.88rem; color: #dc2626;">Please fix the following:</div>
                <ul class="mb-0 ps-3 small" style="color: var(--text-primary); line-height: 1.5;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close ms-1 mt-1 flex-shrink-0" style="font-size: 0.75rem;" onclick="this.closest('.toast-popup').remove()" aria-label="Close"></button>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Auto-dismiss toasts smoothly after 6 seconds
        const toasts = document.querySelectorAll('.toast-popup');
        toasts.forEach(toast => {
            setTimeout(() => {
                toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-15px)';
                setTimeout(() => toast.remove(), 400);
            }, 6000);
        });
    });
</script>
