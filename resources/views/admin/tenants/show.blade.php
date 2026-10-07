@extends('layouts.admin')

@section('title', $tenant->full_name . ' - Tenant Details')
@section('page_title', 'Tenants')
@section('page_subtitle', 'Tenants > Tenant Details')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <a href="{{ route('admin.tenants.index') }}" class="btn-secondary-custom text-decoration-none py-1 px-3" style="font-size: 0.85rem;">
        <i class="bi bi-chevron-left"></i> Back to List
    </a>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.payments.create', ['tenant_id' => $tenant->id]) }}" class="btn btn-outline-success py-1 px-3" style="font-size: 0.85rem; border-radius: 8px;">
            <i class="bi bi-cash-stack me-1"></i> Record Payment
        </a>
        <a href="{{ route('admin.tenants.edit', $tenant->id) }}" class="btn btn-outline-primary py-1 px-3" style="font-size: 0.85rem; border-radius: 8px;">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>

        @if($tenant->status === 'active')
            <button type="button" class="btn btn-warning py-1 px-3 text-dark fw-bold shadow-sm" style="font-size: 0.85rem; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#moveOutModal">
                <i class="bi bi-box-arrow-right me-1"></i> Move Out
            </button>
        @endif

        @if($tenant->status === 'moved_out' || $tenant->status === 'inactive')
            <button type="button" class="btn-danger-custom py-1 px-3" style="font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#deleteTenantModal">
                <i class="bi bi-trash"></i> Delete
            </button>
        @endif
    </div>
</div>

<!-- Tenant Top Profile Banner Card matching wireframe 6. Tenant Details View -->
<div class="profile-banner-card p-4 mb-4">
    <div class="profile-banner-accent"></div>
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-4 flex-wrap flex-sm-nowrap">
                <div class="profile-avatar-frame flex-shrink-0">
                    <img src="{{ $tenant->profile_picture_url }}" alt="{{ $tenant->full_name }}" style="width: 92px; height: 92px;">
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-3 py-1 fw-bold" style="font-size: 0.78rem;">
                            <i class="bi bi-person-badge me-1"></i> {{ $tenant->tenant_code ?? 'TEN-PENDING' }}
                        </span>
                        
                        @if($tenant->status === 'active')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1" style="font-size: 0.78rem;">
                                <i class="bi bi-shield-check me-1"></i> Active Resident
                            </span>
                        @elseif($tenant->status === 'pending')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1" style="font-size: 0.78rem;">
                                <i class="bi bi-hourglass-split me-1"></i> Pending Verification
                            </span>
                        @elseif($tenant->status === 'moved_out')
                            <span class="badge bg-secondary-subtle text-secondary border px-3 py-1" style="font-size: 0.78rem;">
                                <i class="bi bi-box-arrow-right me-1"></i> Moved Out
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1" style="font-size: 0.78rem;">
                                <i class="bi bi-x-circle-fill me-1"></i> Inactive
                            </span>
                        @endif

                        @if($tenant->room)
                            <a href="{{ route('admin.rooms.show', $tenant->room->id) }}" class="assigned-room-chip" style="font-size: 0.8rem;" title="View Room Details">
                                <span class="room-chip-badge">
                                    <i class="bi bi-door-open-fill"></i> Room {{ $tenant->room->room_number }}
                                </span>
                                @if($tenant->room->room_type)
                                    <span class="room-chip-type">{{ $tenant->room->room_type }}</span>
                                @endif
                            </a>
                        @else
                            <span class="unassigned-room-chip">
                                <i class="bi bi-dash-circle me-1"></i> Unassigned Room
                            </span>
                        @endif
                    </div>

                    <h2 class="fw-bold mb-2" style="color: var(--text-primary); letter-spacing: -0.02em; font-size: 1.65rem;">
                        {{ $tenant->full_name }}
                    </h2>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="profile-info-chip">
                            <i class="bi bi-telephone text-primary"></i>
                            <span>{{ $tenant->contact_number ?: 'No phone recorded' }}</span>
                        </span>
                        <span class="profile-info-chip">
                            <i class="bi bi-envelope text-primary"></i>
                            <span>{{ $tenant->user->email ?? 'No email' }}</span>
                        </span>
                        <span class="profile-info-chip">
                            <i class="bi bi-calendar-check text-primary"></i>
                            <span>Resident since {{ $tenant->move_in_date ? $tenant->move_in_date->format('M d, Y') : 'N/A' }}</span>
                        </span>
                        @if($tenant->address)
                        <span class="profile-info-chip text-truncate" style="max-width: 280px;" title="{{ $tenant->address }}">
                            <i class="bi bi-geo-alt text-primary"></i>
                            <span>{{ $tenant->address }}</span>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 text-lg-end">
            <div class="d-flex flex-row flex-lg-column justify-content-lg-end gap-2 flex-wrap">
                <div class="p-3 rounded-3 text-start" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-secondary fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Monthly Rent Rate</span>
                        <i class="bi bi-cash-stack text-success fs-5"></i>
                    </div>
                    <div class="fs-4 fw-bold text-success">
                        ₱{{ number_format($tenant->room->monthly_rent ?? 0, 2) }}
                        <span class="fs-6 text-secondary fw-normal">/mo</span>
                    </div>
                    <div class="small text-secondary mt-1">
                        <i class="bi bi-calendar-event me-1"></i> Rent Due: <strong>Day {{ $tenant->rent_due_day ?? 1 }}</strong> of each month
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation matching wireframe -->
<div class="custom-card p-0 overflow-hidden mb-4">
    <ul class="nav nav-tabs px-3 pt-3 border-bottom" id="tenantTabs" role="tablist" style="border-color: var(--border-color) !important; background-color: var(--table-header-bg);">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" style="color: var(--text-primary); border-radius: 8px 8px 0 0;">
                <i class="bi bi-person me-1"></i> Personal Information
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="lease-tab" data-bs-toggle="tab" data-bs-target="#lease" type="button" role="tab" style="color: var(--text-primary); border-radius: 8px 8px 0 0;">
                <i class="bi bi-key me-1"></i> Lease Information
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab" style="color: var(--text-primary); border-radius: 8px 8px 0 0;">
                <i class="bi bi-credit-card me-1"></i> Payment Summary
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab" style="color: var(--text-primary); border-radius: 8px 8px 0 0;">
                <i class="bi bi-sticky me-1"></i> Notes
            </button>
        </li>
    </ul>

    <div class="tab-content p-4" id="tenantTabsContent">
        <!-- TAB 1: Personal Information -->
        <div class="tab-pane fade show active" id="personal" role="tabpanel">
            <div class="row g-3">
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-person text-primary"></i> Full Name</div>
                        <div class="info-tile-value">{{ $tenant->full_name }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-gender-ambiguous text-primary"></i> Gender</div>
                        <div class="info-tile-value">{{ $tenant->gender ? ucfirst($tenant->gender) : 'Not specified' }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-cake2 text-primary"></i> Date of Birth</div>
                        <div class="info-tile-value">{{ $tenant->date_of_birth ? $tenant->date_of_birth->format('F d, Y') : '-' }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-telephone text-primary"></i> Contact Number</div>
                        <div class="info-tile-value">{{ $tenant->contact_number ?: 'Not provided' }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-envelope text-primary"></i> Email Address</div>
                        <div class="info-tile-value text-truncate" title="{{ $tenant->user->email ?? '-' }}">{{ $tenant->user->email ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-calendar-check text-primary"></i> Move-in Date</div>
                        <div class="info-tile-value">{{ $tenant->move_in_date ? $tenant->move_in_date->format('F d, Y') : '-' }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-flag text-primary"></i> Nationality</div>
                        <div class="info-tile-value">{{ $tenant->nationality ?? 'Filipino' }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-shield-check text-primary"></i> Account Status</div>
                        <div class="info-tile-value">
                            <span class="badge-pill {{ $tenant->status === 'active' ? 'badge-success' : 'badge-secondary' }} px-2 py-1 fs-7">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-telephone-plus text-primary"></i> Emergency Contact</div>
                        <div class="info-tile-value">{{ $tenant->emergency_contact ?: 'None provided' }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-clock-history text-primary"></i> Registered At</div>
                        <div class="info-tile-value">{{ $tenant->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-arrow-repeat text-primary"></i> Last Profile Update</div>
                        <div class="info-tile-value">{{ $tenant->updated_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="info-tile">
                        <div class="info-tile-label"><i class="bi bi-geo-alt text-primary"></i> Home Address</div>
                        <div class="info-tile-value text-truncate" title="{{ $tenant->address ?: 'None provided' }}">{{ $tenant->address ?: 'None provided' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: Lease Information -->
        <div class="tab-pane fade" id="lease" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3 border-bottom pb-2" style="font-size: 0.95rem; border-color: var(--border-color) !important;">
                        Current Room Lease
                    </h6>
                    @if($tenant->room)
                        <div class="p-3 rounded mb-3" style="background: rgba(16, 185, 129, 0.04); border: 1px solid rgba(16, 185, 129, 0.22); font-size: 0.88rem;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary">Assigned Room:</span>
                                <a href="{{ route('admin.rooms.show', $tenant->room->id) }}" class="assigned-room-chip" style="font-size: 0.85rem;" title="View Room Details">
                                    <span class="room-chip-badge">
                                        <i class="bi bi-door-open-fill"></i> Room {{ $tenant->room->room_number }}
                                    </span>
                                </a>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary">Room Type:</span>
                                <span>{{ $tenant->room->room_type }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary">Monthly Rental Rate:</span>
                                <strong class="text-success fs-6">₱{{ number_format($tenant->room->monthly_rent, 2) }} / month</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary">Floor:</span>
                                <span>{{ $tenant->room->floor ?: 'Ground Floor' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-secondary">Monthly Due Day:</span>
                                <strong>Day {{ $tenant->move_in_date ? $tenant->move_in_date->day : '-' }} of each month</strong>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">No room currently assigned.</div>
                    @endif

                    @if($tenant->status === 'active')
                        <!-- Mark as Moved Out Form -->
                        <form action="{{ route('admin.tenants.mark-moved-out', $tenant->id) }}" method="POST" onsubmit="return confirm('Mark this tenant as Moved Out? Future rent dues will stop being scheduled.')" class="mt-3">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label text-danger" style="font-size: 0.82rem;">Move-Out Date:</label>
                                <input type="date" name="move_out_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <button type="submit" class="btn-outline-danger-custom w-100">
                                <i class="bi bi-box-arrow-right"></i> Mark as Moved Out
                            </button>
                        </form>
                    @endif
                </div>

                <div class="col-md-6 border-start-md ps-md-4" style="border-color: var(--border-color) !important;">
                    <h6 class="fw-bold mb-3 border-bottom pb-2" style="font-size: 0.95rem; border-color: var(--border-color) !important;">
                        Transfer Room (Section 81)
                    </h6>
                    <form action="{{ route('admin.tenants.transfer-room', $tenant->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="current_room_id" value="{{ $tenant->room_id }}">

                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.85rem;">Select New Room</label>
                            <select name="new_room_id" class="form-select" required>
                                <option value="">-- Choose New Room --</option>
                                @foreach($availableRooms as $r)
                                    @if($r->id !== $tenant->room_id && $r->is_available)
                                        <option value="{{ $r->id }}">
                                            Room {{ $r->room_number }} ({{ $r->room_type }}) — ₱{{ number_format($r->monthly_rent, 2) }}/mo [{{ $r->available_slots }} slots]
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="form-text" style="font-size: 0.76rem;">
                                Transferring updates room occupancy immediately and sets future monthly rent obligations to the new room rate.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary-custom" {{ $tenant->status !== 'active' ? 'disabled' : '' }}>
                            <i class="bi bi-arrow-left-right"></i> Transfer Room
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- TAB 3: Payment Summary -->
        <div class="tab-pane fade" id="payment" role="tabpanel">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0" style="font-size: 0.95rem;">Payment History & Invoices</h6>
                <a href="{{ route('admin.payments.create') }}?tenant_id={{ $tenant->id }}" class="btn-primary-custom py-1 px-3 text-decoration-none" style="font-size: 0.82rem;">
                    <i class="bi bi-plus-lg"></i> Record Payment
                </a>
            </div>

            <div class="table-responsive">
                <table class="custom-table mb-0">
                    <thead>
                        <tr>
                            <th>Payment Code</th>
                            <th>Billing Period</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date Paid</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenant->payments as $payment)
                            <tr>
                                <td class="fw-semibold">{{ $payment->payment_code ?? ('PAY-' . $payment->id) }}</td>
                                <td>{{ $payment->billing_period_label }}</td>
                                <td class="fw-bold text-success">₱{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $payment->payment_method === 'cash' ? 'bg-secondary' : 'bg-primary' }}">
                                        {{ strtoupper($payment->payment_method) }}
                                    </span>
                                </td>
                                <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '-' }}</td>
                                <td>
                                    @if($payment->status === 'paid')
                                        <span class="badge-pill badge-success"><i class="bi bi-check-circle-fill"></i> Paid</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="badge-pill badge-warning"><i class="bi bi-hourglass-split"></i> Pending Verification</span>
                                    @elseif($payment->status === 'partial')
                                        <span class="badge-pill badge-partial"><i class="bi bi-pie-chart-fill"></i> Partial</span>
                                    @else
                                        <span class="badge-pill badge-danger"><i class="bi bi-x-circle-fill"></i> Rejected</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="action-btn" title="View Payment Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No payment records found for this tenant.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 4: Notes -->
        <div class="tab-pane fade" id="notes" role="tabpanel">
            <h6 class="fw-bold mb-3" style="font-size: 0.95rem;">Admin Notes</h6>
            <div class="p-3 rounded" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.9rem;">
                {{ $tenant->notes ?: 'No notes recorded for this tenant.' }}
            </div>
        </div>
    </div>
</div>

<!-- Move Out Modal -->
<div class="modal fade" id="moveOutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-card p-4 border-0">
            <div class="text-center mb-3">
                <div class="empty-state-icon mx-auto text-warning mb-2" style="width: 56px; height: 56px; background-color: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                    <i class="bi bi-box-arrow-right fs-2"></i>
                </div>
                <h5 class="fw-bold mb-1">Move Out Tenant</h5>
                <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                    Confirm that <strong>{{ $tenant->full_name }}</strong> is moving out.
                </p>
            </div>

            <div class="p-3 rounded mb-3" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.85rem;">
                <div class="fw-bold fs-6 mb-1">{{ $tenant->full_name }}</div>
                <div class="text-secondary">Tenant Code: <strong class="text-primary font-monospace">{{ $tenant->tenant_code ?? '-' }}</strong></div>
                <div class="text-secondary">Current Room: <strong style="color: var(--text-primary);">{{ $tenant->room ? 'Room ' . $tenant->room->room_number : 'Unassigned' }}</strong></div>
                <div class="text-secondary">Move-in Date: {{ $tenant->move_in_date ? $tenant->move_in_date->format('M d, Y') : 'N/A' }}</div>
            </div>

            <form action="{{ route('admin.tenants.mark-moved-out', $tenant->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Official Move-out Date <span class="text-danger">*</span></label>
                    <input type="date" name="move_out_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    <small class="text-muted">Frees up the room slot and officially updates status to <strong>Moved Out</strong>.</small>
                </div>

                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-start gap-2" style="font-size: 0.82rem;">
                    <i class="bi bi-info-circle-fill fs-6 mt-1 flex-shrink-0"></i>
                    <div>Once moved out, future dues stop generating, and the admin will be able to delete this record if needed.</div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold px-3" style="border-radius: 8px;">
                        <i class="bi bi-box-arrow-right me-1"></i> Confirm Move Out
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Blocked Delete Modal (Cannot delete active resident) -->
<div class="modal fade" id="blockedDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-card p-4 border-0">
            <div class="text-center mb-3">
                <div class="empty-state-icon mx-auto text-warning mb-2" style="width: 56px; height: 56px; background-color: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                    <i class="bi bi-shield-exclamation fs-2"></i>
                </div>
                <h5 class="fw-bold mb-1">Cannot Delete Active Resident</h5>
                <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                    <strong>{{ $tenant->full_name }}</strong> is currently an active resident.
                </p>
            </div>

            <div class="alert alert-warning py-3 px-3 mb-3 d-flex align-items-start gap-2" style="font-size: 0.85rem;">
                <i class="bi bi-exclamation-triangle-fill fs-5 mt-1 flex-shrink-0 text-warning"></i>
                <div>
                    <strong>Rule:</strong> Dili maka-delete og tenant samtang Active pa. Kinahanglan i-click una ang <strong>"Move Out"</strong> ayha maka-delete si admin.
                </div>
            </div>

            <div class="p-3 rounded mb-3" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.85rem;">
                <div class="fw-bold fs-6 mb-1">{{ $tenant->full_name }}</div>
                <div class="text-secondary">Tenant ID: {{ $tenant->tenant_code ?? '-' }}</div>
                <div class="text-secondary">Assigned Room: {{ $tenant->room ? 'Room ' . $tenant->room->room_number : 'Unassigned' }}</div>
                <div class="text-secondary">Current Status: <span class="badge bg-success-subtle text-success">Active Resident</span></div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Close</button>
                @if($tenant->status === 'active')
                    <button type="button" class="btn btn-warning text-dark fw-bold px-3" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#moveOutModal" style="border-radius: 8px;">
                        <i class="bi bi-box-arrow-right me-1"></i> Move Out Tenant First
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Tenant Modal matching wireframe 8. Delete Tenant (Confirmation) -->
<div class="modal fade" id="deleteTenantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-card p-4 border-0">
            <div class="text-center mb-3">
                <div class="empty-state-icon mx-auto text-danger mb-2" style="width: 56px; height: 56px;">
                    <i class="bi bi-trash fs-2"></i>
                </div>
                <h5 class="fw-bold mb-1">Delete {{ $tenant->status === 'moved_out' ? 'Moved-Out' : 'Inactive' }} Tenant</h5>
                <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                    Are you sure you want to permanently delete this {{ str_replace('_', ' ', $tenant->status) }} tenant? This action cannot be undone.
                </p>
            </div>

            <div class="p-3 rounded mb-3" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.85rem;">
                <div class="fw-bold fs-6 mb-1">{{ $tenant->full_name }}</div>
                <div class="text-secondary">Tenant ID: {{ $tenant->tenant_code ?? '-' }}</div>
                <div class="text-secondary">Status: <span class="badge {{ $tenant->status === 'moved_out' ? 'bg-secondary' : 'bg-danger' }}">{{ ucfirst(str_replace('_', ' ', $tenant->status)) }}</span></div>
                <div class="text-secondary">{{ $tenant->status === 'moved_out' ? 'Move-out Date: ' . ($tenant->move_out_date ? $tenant->move_out_date->format('M d, Y') : 'Recorded') : 'Status: Inactive' }}</div>
            </div>

            <div class="alert alert-danger py-2 px-3 mb-3 d-flex align-items-start gap-2" style="font-size: 0.82rem;">
                <i class="bi bi-exclamation-triangle-fill fs-6 mt-1"></i>
                <div>All records (payments, lease history) for this {{ str_replace('_', ' ', $tenant->status) }} tenant will be permanently removed.</div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.tenants.destroy', $tenant->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger-custom">Yes, Delete Tenant</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
