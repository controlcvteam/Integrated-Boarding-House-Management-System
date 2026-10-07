<!-- Modal for Viewing Payment Edit History (Tenant Portal) -->
<div class="modal fade" id="tenantPaymentEditHistoryModal" tabindex="-1" aria-labelledby="tenantPaymentEditHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0" style="background-color: var(--bg-card); color: var(--text-primary); border-radius: 16px;">
            <div class="modal-header border-bottom py-3" style="border-color: var(--border-color) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: rgba(124, 58, 237, 0.15); color: #7c3aed;">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title fw-bold mb-0" id="tenantPaymentEditHistoryModalLabel" style="font-size: 1.1rem;">
                                Payment Edit History & Corrections
                            </h5>
                            <span class="badge bg-purple text-white px-2 py-1" id="tenantEditsBadgeCount" style="font-size: 0.72rem;">
                                Revisions
                            </span>
                        </div>
                        <p class="text-secondary mb-0 small" style="font-size: 0.8rem;">
                            Official audit trail of adjustments made by Property Administrator
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Loading State -->
                <div id="tenantHistoryLoadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-purple" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Loading edit history...</span>
                    </div>
                    <div class="text-secondary small mt-3 fw-medium">Loading audit history & revision details...</div>
                </div>

                <!-- Error State -->
                <div id="tenantHistoryErrorAlert" class="alert alert-danger d-none my-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span id="tenantHistoryErrorMessage">Failed to load payment history.</span>
                </div>

                <!-- Details Container -->
                <div id="tenantHistoryDetailsContainer" class="d-none">
                    <!-- Notice Banner -->
                    <div class="p-3 rounded-3 mb-4 d-flex align-items-start gap-3" 
                         style="background: rgba(124, 58, 237, 0.08); border: 1px solid rgba(124, 58, 237, 0.25);">
                        <i class="bi bi-shield-check fs-4 mt-1" style="color: #7c3aed;"></i>
                        <div>
                            <div class="fw-bold" style="color: #7c3aed; font-size: 0.92rem;">Audit-Protected Record</div>
                            <div class="text-secondary small">
                                When payment information is adjusted or corrected by the landlord/admin, the original transaction and every subsequent modification are permanently recorded to ensure complete transparency and accounting accountability.
                            </div>
                        </div>
                    </div>

                    <!-- Comparison Grid: CURRENT vs ORIGINAL -->
                    <div class="row g-3 mb-4">
                        <!-- Current Payment -->
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 h-100" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                                    <span class="fw-bold text-uppercase small text-primary" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                                        <i class="bi bi-check2-circle me-1"></i> Current Payment Record
                                    </span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;" id="tenantCurStatusBadge">
                                        ACTIVE
                                    </span>
                                </div>
                                <div class="small">
                                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--border-color) !important;">
                                        <span class="text-secondary">Payment Code:</span>
                                        <strong id="tenantCurPaymentCode" class="font-monospace text-primary">—</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--border-color) !important;">
                                        <span class="text-secondary">Amount Paid:</span>
                                        <strong id="tenantCurAmount" class="text-success fs-6">—</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--border-color) !important;">
                                        <span class="text-secondary">Rental Period:</span>
                                        <strong id="tenantCurRentalPeriod">—</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--border-color) !important;">
                                        <span class="text-secondary">Payment Method:</span>
                                        <strong id="tenantCurPaymentMethod" class="text-uppercase">—</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--border-color) !important;">
                                        <span class="text-secondary">Payment Date:</span>
                                        <strong id="tenantCurPaymentDate">—</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <span class="text-secondary">Receipt Number:</span>
                                        <strong id="tenantCurReceiptNo" class="font-monospace text-secondary">—</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Original Payment -->
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 h-100" style="background-color: var(--table-header-bg); border: 1px dashed rgba(124, 58, 237, 0.4);">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                                    <span class="fw-bold text-uppercase small text-secondary" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                                        <i class="bi bi-clock-history me-1"></i> Original Entry (Before Edits)
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size: 0.7rem;">
                                        ORIGINAL
                                    </span>
                                </div>
                                <div class="small">
                                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--border-color) !important;">
                                        <span class="text-secondary">Initial Amount:</span>
                                        <strong id="tenantOrigAmount" class="text-secondary">—</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--border-color) !important;">
                                        <span class="text-secondary">Initial Period:</span>
                                        <strong id="tenantOrigRentalPeriod">—</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--border-color) !important;">
                                        <span class="text-secondary">Initial Method:</span>
                                        <strong id="tenantOrigPaymentMethod" class="text-uppercase">—</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--border-color) !important;">
                                        <span class="text-secondary">Initial Date:</span>
                                        <strong id="tenantOrigPaymentDate">—</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--border-color) !important;">
                                        <span class="text-secondary">Initial Status:</span>
                                        <strong id="tenantOrigStatus">—</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <span class="text-secondary">Submitted On:</span>
                                        <span id="tenantOrigCreatedAt" class="text-secondary">—</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revisions Timeline Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3 pt-2 border-top" style="border-color: var(--border-color) !important;">
                        <h6 class="fw-bold mb-0 text-uppercase small" style="color: var(--text-primary); letter-spacing: 0.5px;">
                            <i class="bi bi-journal-text me-1 text-purple"></i> Admin Revision History & Reasons
                        </h6>
                        <span class="text-secondary small">
                            Chronological (Latest First)
                        </span>
                    </div>

                    <!-- Dynamic Revisions Timeline -->
                    <div id="tenantHistoryRevisionsTimeline"></div>
                </div>
            </div>

            <div class="modal-footer border-top py-2 px-4 d-flex justify-content-between align-items-center" style="border-color: var(--border-color) !important;">
                <div>
                    <a id="tenantModalFullHistoryLink" href="#" class="btn btn-sm btn-outline-purple text-decoration-none">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Dedicated History Page
                    </a>
                </div>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
window.openTenantPaymentEditHistoryModal = function(paymentId) {
    const modalEl = document.getElementById('tenantPaymentEditHistoryModal');
    if (!modalEl) return;
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();

    const spinner = document.getElementById('tenantHistoryLoadingSpinner');
    const details = document.getElementById('tenantHistoryDetailsContainer');
    const errorAlert = document.getElementById('tenantHistoryErrorAlert');
    const errorMsg = document.getElementById('tenantHistoryErrorMessage');
    const timeline = document.getElementById('tenantHistoryRevisionsTimeline');
    const fullLink = document.getElementById('tenantModalFullHistoryLink');

    spinner.classList.remove('d-none');
    details.classList.add('d-none');
    if (errorAlert) errorAlert.classList.add('d-none');
    timeline.innerHTML = '';

    const baseUrl = "{{ url('/') }}";
    fullLink.href = `${baseUrl}/tenant/payments/${paymentId}/history`;

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000);

    fetch(`${baseUrl}/tenant/payments/${paymentId}/edit-history`, {
        signal: controller.signal,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            clearTimeout(timeoutId);
            if (!response.ok) {
                throw new Error(`Could not retrieve payment edit history (Status ${response.status})`);
            }
            return response.json();
        })
        .then(data => {
            if (!data || !data.payment) {
                throw new Error('Invalid payment details received.');
            }

            const p = data.payment;
            const orig = data.original_record || {};
            const histories = data.histories || [];

            // Current payment info
            document.getElementById('tenantCurPaymentCode').textContent = p.payment_code || ('PAY-' + p.id);
            document.getElementById('tenantCurAmount').textContent = p.amount || '—';
            document.getElementById('tenantCurRentalPeriod').textContent = p.rental_period || '—';
            document.getElementById('tenantCurPaymentMethod').textContent = p.payment_method || '—';
            document.getElementById('tenantCurPaymentDate').textContent = p.payment_date || '—';
            document.getElementById('tenantCurReceiptNo').textContent = p.receipt_number || '—';

            const curStatusBadge = document.getElementById('tenantCurStatusBadge');
            const statusStr = (p.status || '').toLowerCase();
            curStatusBadge.textContent = (p.status || 'Active').toUpperCase();
            if (statusStr === 'paid' || statusStr === 'verified') {
                curStatusBadge.className = 'badge bg-success-subtle text-success border border-success-subtle';
            } else if (statusStr === 'partial') {
                curStatusBadge.className = 'badge bg-info-subtle text-info border border-info-subtle';
            } else {
                curStatusBadge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle';
            }

            // Original payment info
            document.getElementById('tenantOrigAmount').textContent = orig.amount || '—';
            document.getElementById('tenantOrigRentalPeriod').textContent = orig.rental_period || '—';
            document.getElementById('tenantOrigPaymentMethod').textContent = orig.payment_method || '—';
            document.getElementById('tenantOrigPaymentDate').textContent = orig.date || '—';
            document.getElementById('tenantOrigStatus').textContent = orig.status || '—';
            document.getElementById('tenantOrigCreatedAt').textContent = orig.created_at || '—';

            const editsBadgeCount = document.getElementById('tenantEditsBadgeCount');
            if (editsBadgeCount) {
                const count = data.total_edits || histories.length;
                editsBadgeCount.textContent = `${count} Revision${count === 1 ? '' : 's'}`;
            }

            // Build revisions timeline
            if (histories && histories.length > 0) {
                let html = '';
                histories.forEach(h => {
                    html += `
                        <div class="audit-entry-card is-edit mb-3 shadow-xs">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                                <div>
                                    <span class="badge bg-purple text-white fw-bold px-2 py-1 mb-1" style="font-size: 0.72rem;">
                                        Edit #${h.edit_number}
                                    </span>
                                    <div class="fw-bold" style="color: var(--text-primary); font-size: 0.92rem;">
                                        ${h.date_formatted}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="text-secondary small d-block">Adjusted by:</span>
                                    <span class="fw-semibold text-primary"><i class="bi bi-person-badge me-1"></i>${h.editor_name || 'Landlord'}</span>
                                </div>
                            </div>

                            <div class="mb-3 p-2 rounded" style="background-color: var(--bg-card); border: 1px solid var(--border-color); font-size: 0.85rem;">
                                <span class="fw-bold text-warning text-uppercase small d-block" style="letter-spacing: 0.5px;">
                                    <i class="bi bi-chat-left-quote me-1"></i> Reason for Edit:
                                </span>
                                <div class="fw-semibold mt-1" style="color: var(--text-primary);">
                                    "${h.reason || 'Correction of entry'}"
                                </div>
                            </div>

                            <div class="fw-bold small text-uppercase text-secondary mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                Exact Field Changes:
                            </div>
                    `;

                    const changes = h.changed_fields;
                    if (changes && Object.keys(changes).length > 0) {
                        for (const key in changes) {
                            const c = changes[key];
                            html += `
                                <div class="before-after-box mb-2">
                                <div>
                                    <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.68rem;">
                                        ${c.field_label || key} &bull; BEFORE
                                    </div>
                                    <div class="before-pill mt-1">
                                        ${c.old || '—'}
                                    </div>
                                </div>
                                <div class="text-secondary px-2 fs-5">&rarr;</div>
                                <div>
                                    <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.68rem;">
                                        ${c.field_label || key} &bull; AFTER
                                    </div>
                                    <div class="after-pill mt-1">
                                        ${c.new || '—'}
                                    </div>
                                </div>
                            </div>
                            `;
                        }
                    } else {
                        html += `
                            <div class="text-secondary small fst-italic">No specific field values modified.</div>
                        `;
                    }

                    html += `</div>`;
                });
                timeline.innerHTML = html;
            } else {
                timeline.innerHTML = `
                    <div class="text-center py-3 text-secondary small">
                        No previous revisions recorded.
                    </div>
                `;
            }

            details.classList.remove('d-none');
        })
        .catch(err => {
            clearTimeout(timeoutId);
            if (errorAlert) {
                errorAlert.classList.remove('d-none');
                if (errorMsg) {
                    errorMsg.innerHTML = `${err.name === 'AbortError' ? 'Request timed out loading revision history.' : (err.message || 'Error loading payment history.')} <div class="mt-2"><button type="button" class="btn btn-sm btn-outline-danger" onclick="openTenantPaymentEditHistoryModal(${paymentId})"><i class="bi bi-arrow-clockwise me-1"></i> Retry Loading</button></div>`;
                }
            }
        })
        .finally(() => {
            clearTimeout(timeoutId);
            if (spinner) spinner.classList.add('d-none');
        });
};
</script>
