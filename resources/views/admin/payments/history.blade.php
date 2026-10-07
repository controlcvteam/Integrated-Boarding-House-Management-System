@extends('layouts.admin')

@section('title', 'Edit History: ' . $payment->payment_code)
@section('page_title', 'Payment Audit & Edit History')
@section('page_subtitle', 'Payments > ' . $payment->payment_code . ' > Edit History')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to Payment Details
    </a>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.payments.edit', $payment->id) }}" class="btn-primary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
            <i class="bi bi-pencil"></i> Edit Payment
        </a>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Audit Log
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Current Payment vs Original Payment -->
    <div class="col-lg-5">
        <!-- Current Payment Card -->
        <div class="custom-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2" style="border-color: var(--border-color) !important;">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-credit-card me-1"></i> Current Payment Record
                </h6>
                <span class="badge bg-primary-subtle text-primary fw-bold">CURRENT ACTIVE</span>
            </div>

            <div class="row g-2" style="font-size: 0.88rem;">
                <div class="col-5 text-secondary">Payment ID:</div>
                <div class="col-7 fw-bold">{{ $payment->payment_code ?? ('PAY-' . $payment->id) }}</div>

                <div class="col-5 text-secondary">Tenant:</div>
                <div class="col-7 fw-semibold">{{ $payment->tenant->full_name ?? 'N/A' }}</div>

                <div class="col-5 text-secondary">Room:</div>
                <div class="col-7">Room {{ $payment->room->room_number ?? '-' }} ({{ $payment->room->room_type ?? '' }})</div>

                <div class="col-5 text-secondary">Current Amount:</div>
                <div class="col-7 fw-bold text-success fs-6">₱{{ number_format($payment->amount, 2) }}</div>

                <div class="col-5 text-secondary">Rental Period:</div>
                <div class="col-7 fw-semibold">{{ $payment->billing_period_label }}</div>

                <div class="col-5 text-secondary">Payment Method:</div>
                <div class="col-7">
                    <span class="badge {{ $payment->payment_method === 'cash' ? 'bg-secondary' : 'bg-primary' }}">
                        {{ strtoupper($payment->payment_method) }}
                    </span>
                </div>

                <div class="col-5 text-secondary">Payment Date:</div>
                <div class="col-7 fw-semibold">{{ $payment->payment_date ? $payment->payment_date->format('F d, Y') : '-' }}</div>

                <div class="col-5 text-secondary">Status:</div>
                <div class="col-7">
                    <span class="badge-pill {{ in_array($payment->status, ['paid', 'verified']) ? 'badge-success' : 'badge-warning' }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                    @if($payment->is_edited)
                        <span class="badge-edited ms-1">EDITED</span>
                    @endif
                </div>

                <div class="col-5 text-secondary">Receipt Number:</div>
                <div class="col-7 font-monospace fw-semibold text-primary">#REC-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <!-- Original Payment Card -->
        @php
            $earliest = $payment->earliest_edit_history;
            $orig = $earliest ? ($earliest->old_values ?? []) : [];
        @endphp
        <div class="custom-card mb-4" style="border-left: 4px solid #10b981 !important;">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2" style="border-color: var(--border-color) !important;">
                <h6 class="fw-bold mb-0 text-success">
                    <i class="bi bi-clock-history me-1"></i> Original Payment Record
                </h6>
                <span class="badge bg-success-subtle text-success fw-bold">INITIAL CREATION</span>
            </div>

            <p class="text-secondary small mb-3">
                This is the original payment state prior to any modifications being made.
            </p>

            <div class="row g-2" style="font-size: 0.88rem;">
                <div class="col-5 text-secondary">Original Amount:</div>
                <div class="col-7 fw-bold text-dark">
                    ₱{{ number_format($orig['amount'] ?? $payment->amount, 2) }}
                </div>

                <div class="col-5 text-secondary">Original Period:</div>
                <div class="col-7 fw-semibold">
                    {{ $orig['rental_period'] ?? $payment->billing_period_label }}
                </div>

                <div class="col-5 text-secondary">Original Method:</div>
                <div class="col-7">
                    <span class="badge bg-secondary">
                        {{ strtoupper($orig['payment_method'] ?? $payment->payment_method) }}
                    </span>
                </div>

                <div class="col-5 text-secondary">Original Date:</div>
                <div class="col-7">
                    {{ !empty($orig['payment_date']) ? \Carbon\Carbon::parse($orig['payment_date'])->format('F d, Y') : ($payment->payment_date ? $payment->payment_date->format('F d, Y') : '-') }}
                </div>

                <div class="col-5 text-secondary">Original Status:</div>
                <div class="col-7">
                    {{ ucfirst($orig['status'] ?? $payment->status) }}
                </div>

                <div class="col-5 text-secondary">Created At:</div>
                <div class="col-7 text-muted">
                    {{ $payment->created_at->format('F d, Y — g:i A') }}
                </div>
            </div>
        </div>

        <!-- Non-deletable Guarantee Banner -->
        <div class="p-3 rounded border" style="background-color: var(--table-header-bg); border-color: var(--border-color) !important; font-size: 0.82rem;">
            <div class="d-flex align-items-center gap-2 text-secondary mb-1">
                <i class="bi bi-shield-lock-fill text-success fs-5"></i>
                <strong class="text-dark">Immutable Financial Ledger</strong>
            </div>
            <p class="mb-0 text-secondary">
                In compliance with strict boarding house management accounting standards, all recorded changes are permanently archived. Edit history entries cannot be removed or overwritten.
            </p>
        </div>
    </div>

    <!-- Right Column: Complete Edit Timeline (Multiple Edits) -->
    <div class="col-lg-7">
        <div class="custom-card">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2" style="border-color: var(--border-color) !important;">
                <div>
                    <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                        <i class="bi bi-journal-check text-purple me-2"></i> Complete Edit History
                    </h5>
                    <p class="text-secondary small mb-0">Total recorded revisions: {{ $payment->editHistories->count() }}</p>
                </div>
                <span class="badge bg-purple-subtle text-purple border border-purple px-2 py-1 font-monospace">AUDIT LOG</span>
            </div>

            @php
                $histories = $payment->editHistories;
                $totalCount = $histories->count();
            @endphp

            @forelse($histories as $index => $history)
                @php
                    $editNumber = $totalCount - $index;
                @endphp
                <div class="audit-entry-card is-edit mb-4 shadow-xs">
                    <!-- Edit Header -->
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                        <div>
                            <span class="badge bg-purple text-white fw-bold px-2 py-1 mb-1" style="font-size: 0.78rem;">
                                Edit #{{ $editNumber }}
                            </span>
                            <div class="fw-bold" style="color: var(--text-primary); font-size: 0.95rem;">
                                {{ $history->created_at->format('F j, Y — g:i A') }}
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="text-secondary small d-block">Edited by:</span>
                            <span class="fw-semibold text-primary"><i class="bi bi-person-check me-1"></i> {{ $history->editor_display_name }}</span>
                        </div>
                    </div>

                    <!-- Reason for Edit -->
                    <div class="mb-3 p-2 rounded" style="background-color: var(--bg-card); border: 1px solid var(--border-color); font-size: 0.88rem;">
                        <span class="fw-bold text-warning text-uppercase small d-block" style="letter-spacing: 0.5px;">
                            <i class="bi bi-chat-left-quote me-1"></i> Reason for Edit:
                        </span>
                        <div class="fw-semibold mt-1" style="color: var(--text-primary);">
                            "{{ $history->reason }}"
                        </div>
                    </div>

                    <!-- SHOW EXACT CHANGES: Before & After -->
                    <h6 class="fw-bold small text-uppercase text-secondary mb-2" style="letter-spacing: 0.5px;">
                        Exact Field Changes
                    </h6>

                    @if(!empty($history->changed_fields) && count($history->changed_fields) > 0)
                        @foreach($history->changed_fields as $fieldKey => $change)
                            <div class="before-after-box mb-2">
                                <div>
                                    <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.7rem;">
                                        {{ $change['field_label'] ?? ucfirst($fieldKey) }} &bull; BEFORE
                                    </div>
                                    <div class="before-pill mt-1">
                                        {{ $change['old'] ?? '—' }}
                                    </div>
                                </div>
                                <div class="text-secondary px-2 fs-5">
                                    &rarr;
                                </div>
                                <div>
                                    <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.7rem;">
                                        {{ $change['field_label'] ?? ucfirst($fieldKey) }} &bull; AFTER
                                    </div>
                                    <div class="after-pill mt-1">
                                        {{ $change['new'] ?? '—' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-secondary py-2 small mb-0">
                            General correction recorded (re-verified without field delta).
                        </div>
                    @endif
                </div>
            @empty
                <!-- No edits yet -->
                <div class="empty-state text-center py-5">
                    <div class="empty-state-icon mx-auto mb-2 text-success" style="width: 52px; height: 52px;">
                        <i class="bi bi-shield-check fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-1">No Edits Recorded</h5>
                    <p class="text-secondary small mb-0">This payment remains in its pristine original recorded state with no subsequent alterations.</p>
                </div>
            @endforelse

            <!-- ORIGINAL RECORD (Baseline) -->
            <div class="audit-entry-card is-original shadow-xs mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                    <div>
                        <span class="badge bg-success text-white fw-bold px-2 py-1 mb-1" style="font-size: 0.78rem;">
                            ORIGINAL RECORD
                        </span>
                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                            {{ $payment->created_at->format('F j, Y — g:i A') }}
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="text-secondary small d-block">Initially Recorded By:</span>
                        <span class="fw-semibold text-dark"><i class="bi bi-person me-1"></i> {{ $payment->receiver->name ?? 'System' }}</span>
                    </div>
                </div>

                <div class="row g-2 mt-1" style="font-size: 0.85rem;">
                    <div class="col-sm-4 text-secondary">Initial Amount:</div>
                    <div class="col-sm-8 fw-bold text-success">₱{{ number_format($orig['amount'] ?? $payment->amount, 2) }}</div>

                    <div class="col-sm-4 text-secondary">Initial Method:</div>
                    <div class="col-sm-8">{{ strtoupper($orig['payment_method'] ?? $payment->payment_method) }}</div>

                    <div class="col-sm-4 text-secondary">Initial Period:</div>
                    <div class="col-sm-8">{{ $orig['rental_period'] ?? $payment->billing_period_label }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
