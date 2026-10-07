@extends('layouts.admin')

@section('title', 'Payments')
@section('page_title', 'Payments')
@section('page_subtitle', 'Payments > Payment List')

@section('content')
<!-- Top Information Banner matching wireframe -->
<div class="custom-card py-3 px-4 mb-4 d-flex align-items-center justify-content-between" style="background-color: var(--table-header-bg);">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle border" 
             style="width: 36px; height: 36px; background-color: var(--bg-card); color: #10b981;">
            <i class="bi bi-shield-check fs-5"></i>
        </div>
        <div>
            <div class="fw-bold" style="font-size: 0.95rem; color: var(--text-primary);">Financial Ledger & Verified Payment Records</div>
            <div class="text-secondary" style="font-size: 0.82rem;">Audit-controlled payment transactions with immutable change history. Records cannot be deleted.</div>
        </div>
    </div>
</div>

<!-- Payment Status Quick Filter Tabs -->
<div class="status-pill-scroll-container mb-3 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
    <span class="text-secondary fw-semibold me-1 flex-shrink-0" style="font-size: 0.82rem;">
        <i class="bi bi-funnel-fill text-primary"></i> Quick Filter:
    </span>

    <a href="{{ route('admin.payments.index', array_merge(request()->except(['status', 'page']), [])) }}" 
       class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }} d-inline-flex align-items-center gap-1"
       style="border-radius: 20px; font-size: 0.8rem; font-weight: 500; padding: 0.3rem 0.85rem;">
        <span>All Payments</span>
        <span class="badge {{ !request('status') ? 'bg-white text-primary' : 'bg-secondary text-white' }} rounded-pill" style="font-size: 0.7rem;">
            {{ $paymentCounts['all'] ?? 0 }}
        </span>
    </a>

    <a href="{{ route('admin.payments.index', array_merge(request()->except(['status', 'page']), ['status' => 'pending'])) }}" 
       class="btn btn-sm btn-tab-pending {{ request('status') === 'pending' ? 'active' : '' }} d-inline-flex align-items-center gap-1">
        <i class="bi bi-hourglass-split"></i>
        <span>Pending</span>
        <span class="badge {{ request('status') === 'pending' ? 'bg-white text-dark' : 'bg-warning text-dark' }} rounded-pill" style="font-size: 0.7rem;">
            {{ $paymentCounts['pending'] ?? 0 }}
        </span>
    </a>

    <a href="{{ route('admin.payments.index', array_merge(request()->except(['status', 'page']), ['status' => 'partial'])) }}" 
       class="btn btn-sm btn-tab-partial {{ request('status') === 'partial' ? 'active' : '' }} d-inline-flex align-items-center gap-1">
        <i class="bi bi-pie-chart-fill"></i>
        <span>Partial</span>
        <span class="badge {{ request('status') === 'partial' ? 'bg-white text-dark' : 'bg-warning text-dark' }} rounded-pill" style="font-size: 0.7rem;">
            {{ $paymentCounts['partial'] ?? 0 }}
        </span>
    </a>

    <a href="{{ route('admin.payments.index', array_merge(request()->except(['status', 'page']), ['status' => 'rejected'])) }}" 
       class="btn btn-sm {{ request('status') === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }} d-inline-flex align-items-center gap-1"
       style="border-radius: 20px; font-size: 0.8rem; font-weight: 500; padding: 0.3rem 0.85rem;">
        <i class="bi bi-x-circle-fill"></i>
        <span>Rejected</span>
        <span class="badge {{ request('status') === 'rejected' ? 'bg-white text-danger' : 'bg-danger text-white' }} rounded-pill" style="font-size: 0.7rem;">
            {{ $paymentCounts['rejected'] ?? 0 }}
        </span>
    </a>

    <a href="{{ route('admin.payments.index', array_merge(request()->except(['status', 'page']), ['status' => 'paid'])) }}" 
       class="btn btn-sm {{ in_array(request('status'), ['paid', 'verified']) ? 'btn-success' : 'btn-outline-success' }} d-inline-flex align-items-center gap-1"
       style="border-radius: 20px; font-size: 0.8rem; font-weight: 500; padding: 0.3rem 0.85rem;">
        <i class="bi bi-check-circle-fill"></i>
        <span>Paid / Verified</span>
        <span class="badge {{ in_array(request('status'), ['paid', 'verified']) ? 'bg-white text-success' : 'bg-success text-white' }} rounded-pill" style="font-size: 0.7rem;">
            {{ $paymentCounts['paid'] ?? 0 }}
        </span>
    </a>
</div>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <form action="{{ route('admin.payments.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 flex-grow-1 filter-form-mobile" style="max-width: 800px;">
        <div class="input-group flex-grow-1" style="min-width: 200px; max-width: 280px;">
            <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                   placeholder="Search payments..." value="{{ request('search') }}">
        </div>

        <select name="payment_method" class="form-select flex-grow-1 flex-sm-grow-0" style="width: auto;" onchange="this.form.submit()">
            <option value="">All Methods</option>
            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="gcash" {{ request('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
        </select>

        <select name="status" class="form-select flex-grow-1 flex-sm-grow-0" style="width: auto;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Verification</option>
            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partially Paid</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        @if(request()->anyFilled(['search', 'payment_method', 'status', 'month', 'year']))
            <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-link text-secondary text-decoration-none">
                <i class="bi bi-x-circle"></i> Clear
            </a>
        @endif
    </form>

    <div>
        <a href="{{ route('admin.payments.create') }}" class="btn-primary-custom text-decoration-none d-inline-flex align-items-center justify-content-center w-100">
            <i class="bi bi-plus-lg"></i> Record Payment
        </a>
    </div>
</div>

<div class="custom-card p-0 overflow-hidden">
    <div class="table-responsive">
        <div class="table-scroll-hint">
            <i class="bi bi-arrows-expand"></i> Swipe table horizontally to see all columns
        </div>
        <table class="custom-table mb-0">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Tenant</th>
                    <th>Room</th>
                    <th>Amount</th>
                    <th>Rental Period</th>
                    <th>Payment Method</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Edit Indicator</th>
                    <th>Receipt</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <!-- Payment ID -->
                        <td class="fw-semibold text-primary">
                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-decoration-none" style="color: inherit;">
                                {{ $payment->payment_code ?? ('PAY-' . $payment->id) }}
                            </a>
                        </td>

                        <!-- Tenant -->
                        <td class="fw-semibold">
                            @if($payment->tenant_id && $payment->tenant)
                                <a href="{{ route('admin.tenants.show', $payment->tenant_id) }}" class="text-decoration-none" style="color: var(--text-primary);">
                                    {{ $payment->tenant->full_name }}
                                </a>
                            @else
                                <span class="text-muted">{{ $payment->tenant->full_name ?? 'Tenant record removed' }}</span>
                            @endif
                        </td>

                        <!-- Room -->
                        <td>
                            @if($payment->room)
                                <a href="{{ route('admin.rooms.show', $payment->room->id) }}" class="assigned-room-chip" style="font-size: 0.8rem;" title="View Room Details">
                                    <span class="room-chip-badge">
                                        <i class="bi bi-door-open-fill"></i>
                                        Room {{ $payment->room->room_number }}
                                    </span>
                                </a>
                            @else
                                <span class="unassigned-room-chip">-</span>
                            @endif
                        </td>

                        <!-- Amount -->
                        <td class="fw-bold text-success text-nowrap">
                            ₱{{ number_format($payment->amount, 2) }}
                        </td>

                        <!-- Rental Period -->
                        <td class="text-nowrap fw-semibold" style="font-size: 0.85rem;">
                            {{ $payment->billing_period_label }}
                        </td>

                        <!-- Payment Method -->
                        <td>
                            @if($payment->payment_method === 'gcash')
                                <span class="badge bg-primary text-white" style="font-size: 0.72rem;">
                                    <i class="bi bi-phone"></i> GCASH
                                </span>
                                @if($payment->gcash_reference)
                                    <div class="text-secondary font-monospace" style="font-size: 0.68rem;">Ref: {{ $payment->gcash_reference }}</div>
                                @endif
                            @else
                                <span class="badge bg-secondary text-white" style="font-size: 0.72rem;">
                                    <i class="bi bi-cash-stack"></i> CASH
                                </span>
                            @endif
                        </td>

                        <!-- Date & Time -->
                        <td class="text-nowrap" style="font-size: 0.85rem;">
                            <div>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '-' }}</div>
                            <div class="text-secondary" style="font-size: 0.72rem;">
                                <i class="bi bi-clock me-1"></i>{{ $payment->formatted_payment_time }}
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="text-nowrap">
                            @if(in_array($payment->status, ['paid', 'verified']))
                                <span class="badge-pill badge-success"><i class="bi bi-check-circle-fill"></i> Paid</span>
                            @elseif($payment->status === 'partial')
                                <span class="badge-pill badge-partial"><i class="bi bi-pie-chart-fill"></i> Partial</span>
                            @elseif($payment->status === 'pending')
                                <span class="badge-pill badge-warning"><i class="bi bi-hourglass-split"></i> Pending</span>
                            @else
                                <span class="badge-pill badge-danger"><i class="bi bi-x-circle-fill"></i> Rejected</span>
                            @endif
                        </td>

                        <!-- Edit Indicator (Clickable EDITED Badge) -->
                        <td class="text-nowrap">
                            @if($payment->is_edited)
                                <button type="button" class="badge-edited border-0" 
                                        onclick="openPaymentEditHistoryModal({{ $payment->id }})" 
                                        title="Click to view complete edit history & revisions">
                                    <i class="bi bi-clock-history"></i> EDITED
                                </button>
                            @else
                                <span class="text-muted small ps-2">&mdash;</span>
                            @endif
                        </td>

                        <!-- Receipt -->
                        <td class="text-nowrap">
                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2 text-nowrap" style="font-size: 0.75rem;">
                                <i class="bi bi-receipt me-1"></i> #REC-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}
                            </a>
                        </td>

                        <!-- Actions (View Details, Edit - No Delete Button!) -->
                        <td class="text-end">
                            <div class="d-inline-flex gap-1 align-items-center table-actions-nowrap">
                                <a href="{{ route('admin.payments.show', $payment->id) }}" class="action-btn" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.payments.edit', $payment->id) }}" class="action-btn" title="Edit Payment">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="bi bi-credit-card-2-front fs-1"></i>
                                </div>
                                <h4 class="empty-state-title">No payment records found.</h4>
                                <p class="empty-state-desc">There are no payment records matching your filter or search criteria.</p>
                                <a href="{{ route('admin.payments.create') }}" class="btn-primary-custom text-decoration-none">
                                    <i class="bi bi-plus-lg"></i> Record New Payment
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
        <div class="p-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-color: var(--border-color) !important;">
            <div class="text-secondary" style="font-size: 0.82rem;">
                Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} payments
            </div>
            <div>
                {{ $payments->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Complete Edit History Modal -->
<div class="modal fade" id="paymentEditHistoryModal" tabindex="-1" aria-labelledby="editHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content custom-card border-0 shadow-lg p-0 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header border-bottom py-3 px-4" style="background-color: var(--table-header-bg); border-color: var(--border-color) !important;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-purple text-white px-2 py-1 font-monospace fw-bold" style="font-size: 0.75rem;">
                        <i class="bi bi-clock-history me-1"></i> AUDIT TRAIL
                    </span>
                    <h5 class="modal-title fw-bold mb-0" id="editHistoryModalLabel" style="color: var(--text-primary);">
                        Payment Edit History & Corrections
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4" id="modalHistoryContent" style="max-height: 75vh; overflow-y: auto;">
                <div class="text-center py-5" id="historyLoadingSpinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading edit history...</span>
                    </div>
                    <p class="text-secondary small mt-2">Loading complete revision audit log...</p>
                </div>

                <!-- Error State -->
                <div id="historyErrorAlert" class="alert alert-danger d-none my-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <span id="historyErrorMessage" class="fw-medium">Failed to load payment history.</span>
                    </div>
                    <div class="mt-2" id="historyRetryContainer"></div>
                </div>

                <div id="historyDetailsContainer" class="d-none">
                    <!-- Section: Current vs Original Overview -->
                    <div class="row g-3 mb-4">
                        <!-- Current Payment -->
                        <div class="col-md-6">
                            <div class="p-3 rounded border h-100" style="background-color: var(--table-header-bg); border-color: var(--border-color) !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom" style="border-color: var(--border-color) !important;">
                                    <span class="fw-bold text-primary small text-uppercase" style="letter-spacing: 0.5px;">CURRENT PAYMENT</span>
                                    <span class="badge bg-primary text-white" style="font-size: 0.65rem;">ACTIVE RECORD</span>
                                </div>
                                <div class="row g-1 small" style="font-size: 0.83rem;">
                                    <div class="col-5 text-secondary">Payment ID:</div>
                                    <div class="col-7 fw-bold" id="curPaymentCode">-</div>

                                    <div class="col-5 text-secondary">Tenant:</div>
                                    <div class="col-7 fw-semibold" id="curTenantName">-</div>

                                    <div class="col-5 text-secondary">Room:</div>
                                    <div class="col-7" id="curRoomNumber">-</div>

                                    <div class="col-5 text-secondary">Amount:</div>
                                    <div class="col-7 fw-bold text-success fs-6" id="curAmount">-</div>

                                    <div class="col-5 text-secondary">Rental Period:</div>
                                    <div class="col-7 fw-semibold" id="curRentalPeriod">-</div>

                                    <div class="col-5 text-secondary">Payment Method:</div>
                                    <div class="col-7 fw-semibold" id="curPaymentMethod">-</div>

                                    <div class="col-5 text-secondary">Payment Date:</div>
                                    <div class="col-7" id="curPaymentDate">-</div>

                                    <div class="col-5 text-secondary">Status:</div>
                                    <div class="col-7" id="curStatus">-</div>

                                    <div class="col-5 text-secondary">Receipt Number:</div>
                                    <div class="col-7 font-monospace text-primary fw-semibold" id="curReceiptNo">-</div>
                                </div>
                            </div>
                        </div>

                        <!-- Original Payment -->
                        <div class="col-md-6">
                            <div class="p-3 rounded border h-100" style="background-color: var(--table-header-bg); border-left: 4px solid #10b981 !important; border-color: var(--border-color) !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom" style="border-color: var(--border-color) !important;">
                                    <span class="fw-bold text-success small text-uppercase" style="letter-spacing: 0.5px;">ORIGINAL PAYMENT</span>
                                    <span class="badge bg-success text-white" style="font-size: 0.65rem;">INITIAL RECORD</span>
                                </div>
                                <div class="row g-1 small" style="font-size: 0.83rem;">
                                    <div class="col-5 text-secondary">Original Amount:</div>
                                    <div class="col-7 fw-bold text-dark fs-6" id="origAmount">-</div>

                                    <div class="col-5 text-secondary">Original Period:</div>
                                    <div class="col-7 fw-semibold" id="origRentalPeriod">-</div>

                                    <div class="col-5 text-secondary">Original Method:</div>
                                    <div class="col-7 fw-semibold" id="origPaymentMethod">-</div>

                                    <div class="col-5 text-secondary">Original Date:</div>
                                    <div class="col-7" id="origPaymentDate">-</div>

                                    <div class="col-5 text-secondary">Original Status:</div>
                                    <div class="col-7" id="origStatus">-</div>

                                    <div class="col-5 text-secondary">Recorded At:</div>
                                    <div class="col-7 text-muted" id="origCreatedAt">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Revisions List (Multiple Edits) -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-uppercase text-secondary small" style="letter-spacing: 0.5px;">
                                <i class="bi bi-list-check me-1"></i> Recorded Edits & Field Deltas
                            </h6>
                            <span class="badge bg-secondary-subtle text-secondary" id="editsBadgeCount">0 Revisions</span>
                        </div>

                        <div id="historyRevisionsTimeline">
                            <!-- Filled dynamically via JavaScript -->
                        </div>
                    </div>

                    <!-- Permanent Record Banner -->
                    <div class="p-3 rounded bg-light border text-muted small d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <span>Audit Protection: Edit records are permanently retained in the system log and cannot be deleted or overwritten.</span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-top py-2 px-4 justify-content-between" style="border-color: var(--border-color) !important;">
                <a href="#" id="modalFullHistoryLink" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Open Full History Page
                </a>
                <button type="button" class="btn btn-secondary-custom py-1 px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
window.openPaymentEditHistoryModal = function(paymentId) {
    const modalEl = document.getElementById('paymentEditHistoryModal');
    if (!modalEl) return;
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();

    const spinner = document.getElementById('historyLoadingSpinner');
    const details = document.getElementById('historyDetailsContainer');
    const errorAlert = document.getElementById('historyErrorAlert');
    const errorMsg = document.getElementById('historyErrorMessage');
    const retryContainer = document.getElementById('historyRetryContainer');
    const timeline = document.getElementById('historyRevisionsTimeline');
    const fullLink = document.getElementById('modalFullHistoryLink');

    spinner.classList.remove('d-none');
    details.classList.add('d-none');
    if (errorAlert) errorAlert.classList.add('d-none');
    timeline.innerHTML = '';
    
    const baseUrl = "{{ url('/') }}";
    fullLink.href = `${baseUrl}/admin/payments/${paymentId}/history`;

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000);

    fetch(`${baseUrl}/admin/payments/${paymentId}/edit-history`, {
        signal: controller.signal,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        clearTimeout(timeoutId);
        if (!response.ok) {
            throw new Error(`Failed to load payment history (Status ${response.status})`);
        }
        return response.json();
    })
    .then(data => {
        if (!data || !data.payment) {
            throw new Error('Invalid payment data received.');
        }

        const p = data.payment;
        const orig = data.original_record || {};
        const histories = data.histories || [];

        // Current payment info
        document.getElementById('curPaymentCode').textContent = p.payment_code || ('PAY-' + p.id);
        document.getElementById('curTenantName').textContent = p.tenant_name || 'N/A';
        document.getElementById('curRoomNumber').textContent = p.room_number ? `${p.room_number} ${p.room_type ? '(' + p.room_type + ')' : ''}` : 'Unassigned';
        document.getElementById('curAmount').textContent = p.amount || '—';
        document.getElementById('curRentalPeriod').textContent = p.rental_period || '—';
        document.getElementById('curPaymentMethod').textContent = p.payment_method || '—';
        document.getElementById('curPaymentDate').textContent = p.payment_date || '—';
        document.getElementById('curStatus').textContent = p.status || '—';
        document.getElementById('curReceiptNo').textContent = p.receipt_number || '—';

        // Original payment info
        document.getElementById('origAmount').textContent = orig.amount || '—';
        document.getElementById('origRentalPeriod').textContent = orig.rental_period || '—';
        document.getElementById('origPaymentMethod').textContent = orig.payment_method || '—';
        document.getElementById('origPaymentDate').textContent = orig.date || '—';
        document.getElementById('origStatus').textContent = orig.status || '—';
        document.getElementById('origCreatedAt').textContent = orig.created_at || '—';

        const editsBadge = document.getElementById('editsBadgeCount');
        if (editsBadge) {
            editsBadge.textContent = `${data.total_edits || histories.length} Revision${(data.total_edits || histories.length) === 1 ? '' : 's'}`;
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
                                <span class="text-secondary small d-block">Edited by:</span>
                                <span class="fw-semibold text-primary"><i class="bi bi-person-check me-1"></i>${h.editor_name || 'Landlord'}</span>
                            </div>
                        </div>

                        <div class="mb-3 p-2 rounded" style="background-color: var(--bg-card); border: 1px solid var(--border-color); font-size: 0.85rem;">
                            <span class="fw-bold text-warning text-uppercase small d-block" style="letter-spacing: 0.5px;">
                                <i class="bi bi-chat-left-quote me-1"></i> Reason for Edit:
                            </span>
                            <div class="fw-semibold mt-1" style="color: var(--text-primary);">
                                "${h.reason || 'No reason specified'}"
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
                        <div class="alert alert-secondary py-1 px-2 small mb-0">
                            General record confirmation (no individual field delta).
                        </div>
                    `;
                }

                html += `</div>`;
            });

            // Baseline original record
            html += `
                <div class="audit-entry-card is-original shadow-xs mt-2">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom" style="border-color: var(--border-color) !important;">
                        <div>
                            <span class="badge bg-success text-white fw-bold px-2 py-1" style="font-size: 0.72rem;">
                                ORIGINAL RECORD
                            </span>
                            <div class="fw-bold text-dark mt-1" style="font-size: 0.88rem;">
                                ${orig.created_at || '—'}
                            </div>
                        </div>
                        <span class="badge bg-success-subtle text-success">Creation Baseline</span>
                    </div>
                    <div class="small text-secondary">
                        Amount: <strong>${orig.amount || '—'}</strong> &bull; Period: <strong>${orig.rental_period || '—'}</strong> &bull; Method: <strong>${orig.payment_method || '—'}</strong>
                    </div>
                </div>
            `;

            timeline.innerHTML = html;
        } else {
            timeline.innerHTML = `<div class="alert alert-info small">No revisions recorded yet.</div>`;
        }

        details.classList.remove('d-none');
    })
    .catch(err => {
        clearTimeout(timeoutId);
        if (errorAlert) {
            errorAlert.classList.remove('d-none');
            if (errorMsg) errorMsg.textContent = err.name === 'AbortError' ? 'Request timed out while loading history. Please check your connection and retry.' : (err.message || 'Error loading payment history.');
            if (retryContainer) {
                retryContainer.innerHTML = `<button type="button" class="btn btn-sm btn-outline-danger" onclick="openPaymentEditHistoryModal(${paymentId})"><i class="bi bi-arrow-clockwise me-1"></i> Retry Loading</button>`;
            }
        } else {
            timeline.innerHTML = `<div class="alert alert-danger">Error loading history data: ${err.message}</div>`;
            details.classList.remove('d-none');
        }
    })
    .finally(() => {
        clearTimeout(timeoutId);
        if (spinner) spinner.classList.add('d-none');
    });
};
</script>
@endsection
