@extends('layouts.admin')

@section('title', 'Tenants')
@section('page_title', 'Tenants')
@section('page_subtitle', 'Manage all tenant records')

@section('content')
<!-- Payment Status Arrangement & Filter Tabs -->
<div class="status-pill-scroll-container mb-3 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
    <span class="text-secondary fw-semibold me-1 flex-shrink-0" style="font-size: 0.82rem;">
        <i class="bi bi-arrow-down-up text-primary"></i> Arrange & Filter:
    </span>

    <a href="{{ route('admin.tenants.index', array_merge(request()->except(['payment_status', 'page']), ['payment_status' => 'all'])) }}" 
       class="btn btn-sm {{ !request('payment_status') || request('payment_status') === 'all' ? 'btn-primary' : 'btn-outline-secondary' }} d-inline-flex align-items-center gap-1"
       style="border-radius: 20px; font-size: 0.8rem; font-weight: 500; padding: 0.3rem 0.85rem;">
        <span>All Tenants</span>
        <span class="badge {{ !request('payment_status') || request('payment_status') === 'all' ? 'bg-white text-primary' : 'bg-secondary text-white' }} rounded-pill" style="font-size: 0.7rem;">
            {{ $paymentCounts['all'] ?? 0 }}
        </span>
    </a>

    <a href="{{ route('admin.tenants.index', array_merge(request()->except(['payment_status', 'page']), ['payment_status' => 'to_pay'])) }}" 
       class="btn btn-sm {{ in_array(request('payment_status'), ['to_pay', 'who_pay']) ? 'btn-danger' : 'btn-outline-danger' }} d-inline-flex align-items-center gap-1"
       style="border-radius: 20px; font-size: 0.8rem; font-weight: 500; padding: 0.3rem 0.85rem;">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span>Who Pay (Unpaid)</span>
        <span class="badge {{ in_array(request('payment_status'), ['to_pay', 'who_pay']) ? 'bg-white text-danger' : 'bg-danger text-white' }} rounded-pill" style="font-size: 0.7rem;">
            {{ $paymentCounts['to_pay'] ?? 0 }}
        </span>
    </a>

    <a href="{{ route('admin.tenants.index', array_merge(request()->except(['payment_status', 'page']), ['payment_status' => 'pending'])) }}" 
       class="btn btn-sm btn-tab-pending {{ request('payment_status') === 'pending' ? 'active' : '' }} d-inline-flex align-items-center gap-1">
        <i class="bi bi-hourglass-split"></i>
        <span>Pending</span>
        <span class="badge {{ request('payment_status') === 'pending' ? 'bg-white text-dark' : 'bg-warning text-dark' }} rounded-pill" style="font-size: 0.7rem;">
            {{ $paymentCounts['pending'] ?? 0 }}
        </span>
    </a>

    <a href="{{ route('admin.tenants.index', array_merge(request()->except(['payment_status', 'page']), ['payment_status' => 'partial'])) }}" 
       class="btn btn-sm btn-tab-partial {{ request('payment_status') === 'partial' ? 'active' : '' }} d-inline-flex align-items-center gap-1">
        <i class="bi bi-pie-chart-fill"></i>
        <span>Partial</span>
        <span class="badge {{ request('payment_status') === 'partial' ? 'bg-white text-dark' : 'bg-warning text-dark' }} rounded-pill" style="font-size: 0.7rem;">
            {{ $paymentCounts['partial'] ?? 0 }}
        </span>
    </a>

    <a href="{{ route('admin.tenants.index', array_merge(request()->except(['payment_status', 'page']), ['payment_status' => 'rejected'])) }}" 
       class="btn btn-sm {{ in_array(request('payment_status'), ['rejected', 'reject']) ? 'btn-danger' : 'btn-outline-danger' }} d-inline-flex align-items-center gap-1"
       style="border-radius: 20px; font-size: 0.8rem; font-weight: 500; padding: 0.3rem 0.85rem;">
        <i class="bi bi-x-circle-fill"></i>
        <span>Reject</span>
        <span class="badge {{ in_array(request('payment_status'), ['rejected', 'reject']) ? 'bg-white text-danger' : 'bg-danger text-white' }} rounded-pill" style="font-size: 0.7rem;">
            {{ $paymentCounts['rejected'] ?? 0 }}
        </span>
    </a>

    <a href="{{ route('admin.tenants.index', array_merge(request()->except(['payment_status', 'page']), ['payment_status' => 'paid'])) }}" 
       class="btn btn-sm {{ request('payment_status') === 'paid' ? 'btn-success' : 'btn-outline-success' }} d-inline-flex align-items-center gap-1"
       style="border-radius: 20px; font-size: 0.8rem; font-weight: 500; padding: 0.3rem 0.85rem;">
        <i class="bi bi-check-circle-fill"></i>
        <span>Paid</span>
        <span class="badge {{ request('payment_status') === 'paid' ? 'bg-white text-success' : 'bg-success text-white' }} rounded-pill" style="font-size: 0.7rem;">
            {{ $paymentCounts['paid'] ?? 0 }}
        </span>
    </a>
</div>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <form action="{{ route('admin.tenants.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 flex-grow-1 filter-form-mobile" style="max-width: 820px;">
        @if(request()->filled('payment_status'))
            <input type="hidden" name="payment_status" value="{{ request('payment_status') }}">
        @endif

        <div class="input-group flex-grow-1" style="min-width: 200px; max-width: 280px;">
            <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                   placeholder="Search name, contact, room..." value="{{ request('search') }}">
        </div>

        <select name="arrange" class="form-select flex-grow-1 flex-sm-grow-0" style="width: auto;" onchange="this.form.submit()">
            <option value="payment_status" {{ ($arrange ?? '') === 'payment_status' ? 'selected' : '' }}>
                Arrange: Who Pay First
            </option>
            <option value="name" {{ ($arrange ?? '') === 'name' ? 'selected' : '' }}>
                Arrange: Name (A-Z)
            </option>
            <option value="name_desc" {{ ($arrange ?? '') === 'name_desc' ? 'selected' : '' }}>
                Arrange: Name (Z-A)
            </option>
        </select>

        <select name="status" class="form-select flex-grow-1 flex-sm-grow-0" style="width: auto;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="moved_out" {{ request('status') == 'moved_out' ? 'selected' : '' }}>Moved Out</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        </select>

        @if($rooms->isNotEmpty())
            <select name="room_id" class="form-select flex-grow-1 flex-sm-grow-0" style="width: auto;" onchange="this.form.submit()">
                <option value="">All Rooms</option>
                @foreach($rooms as $r)
                    <option value="{{ $r->id }}" {{ request('room_id') == $r->id ? 'selected' : '' }}>Room {{ $r->room_number }}</option>
                @endforeach
            </select>
        @endif

        @if(request()->anyFilled(['search', 'status', 'room_id', 'payment_status', 'arrange']))
            <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-link text-secondary text-decoration-none">
                <i class="bi bi-x-circle"></i> Clear
            </a>
        @endif
    </form>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.payments.create') }}" class="btn btn-outline-success text-decoration-none d-inline-flex align-items-center justify-content-center gap-1 shadow-xs flex-grow-1 flex-md-grow-0" style="border-radius: 8px; font-weight: 500; font-size: 0.88rem; padding: 0.45rem 0.9rem;">
            <i class="bi bi-cash-stack"></i> Record Payment
        </a>
        <a href="{{ route('admin.tenants.create') }}" class="btn-primary-custom text-decoration-none d-inline-flex align-items-center justify-content-center flex-grow-1 flex-md-grow-0">
            <i class="bi bi-plus-lg"></i> Add Tenant
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
                    <th>Full Name</th>
                    <th>Room No.</th>
                    <th>Contact No.</th>
                    <th>Tenant Status</th>
                    <th>Payment Status ({{ \Carbon\Carbon::createFromDate($currentYear ?? now()->year, $currentMonth ?? now()->month, 1)->format('M Y') }})</th>
                    <th>Move-in Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                    @php
                        $rentInfo = $tenant->current_rent_info ?? $tenant->getRentStatusForMonthYear($currentMonth ?? now()->month, $currentYear ?? now()->year);
                        $payCat = $tenant->current_payment_category ?? $tenant->getPaymentStatusCategory($currentMonth ?? now()->month, $currentYear ?? now()->year);
                    @endphp
                    <tr>
                        <td class="fw-bold" style="color: var(--text-primary);">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $tenant->profile_picture_url }}" alt="{{ $tenant->full_name }}" class="rounded-circle border shadow-xs flex-shrink-0" style="width: 36px; height: 36px; object-fit: cover;">
                                <div>
                                    <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="text-decoration-none" style="color: var(--text-primary);">
                                        {{ $tenant->full_name }}
                                    </a>
                                    <div class="text-secondary fw-normal" style="font-size: 0.75rem;">
                                        {{ $tenant->tenant_code ?? 'Tenant' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($tenant->room)
                                <a href="{{ route('admin.rooms.show', $tenant->room->id) }}" class="assigned-room-chip" title="View Room Details">
                                    <span class="room-chip-badge">
                                        <i class="bi bi-door-open-fill"></i>
                                        Room {{ $tenant->room->room_number }}
                                    </span>
                                    @if($tenant->room->room_type)
                                        <span class="room-chip-type">{{ $tenant->room->room_type }}</span>
                                    @endif
                                </a>
                            @else
                                <span class="unassigned-room-chip">
                                    <i class="bi bi-dash-circle"></i> Unassigned
                                </span>
                            @endif
                        </td>
                        <td>{{ $tenant->contact_number }}</td>
                        <td>
                            @if($tenant->status === 'active')
                                <span class="badge-pill badge-success"><i class="bi bi-shield-check"></i> Active</span>
                            @elseif($tenant->status === 'pending')
                                <span class="badge-pill badge-warning"><i class="bi bi-hourglass-split"></i> Pending</span>
                            @elseif($tenant->status === 'moved_out')
                                <span class="badge-pill badge-secondary"><i class="bi bi-box-arrow-right"></i> Moved Out</span>
                            @else
                                <span class="badge-pill badge-danger"><i class="bi bi-x-circle-fill"></i> Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($payCat === 'paid')
                                <span class="badge-pill badge-success" title="Fully paid for this month">
                                    <i class="bi bi-check-circle-fill"></i> Paid
                                </span>
                            @elseif($payCat === 'pending')
                                <span class="badge-pill badge-warning" title="Payment submitted, awaiting admin verification">
                                    <i class="bi bi-hourglass-split"></i> Pending
                                </span>
                            @elseif($payCat === 'partial')
                                <span class="badge-pill badge-partial" title="Partially paid. Remaining balance: ₱{{ number_format($rentInfo['balance'] ?? 0, 2) }}">
                                    <i class="bi bi-pie-chart-fill"></i> Partial (₱{{ number_format($rentInfo['paid_amount'] ?? 0, 2) }})
                                </span>
                            @elseif($payCat === 'rejected')
                                <span class="badge-pill badge-danger" title="Payment was rejected by admin">
                                    <i class="bi bi-x-circle-fill"></i> Rejected
                                </span>
                            @elseif($payCat === 'to_pay')
                                <span class="badge-pill" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;" title="Rent due for this month: ₱{{ number_format($rentInfo['balance'] ?? ($tenant->room->monthly_rent ?? 0), 2) }}">
                                    <i class="bi bi-exclamation-circle-fill"></i> To Pay (₱{{ number_format($rentInfo['balance'] ?? ($tenant->room->monthly_rent ?? 0), 2) }})
                                </span>
                            @else
                                <span class="text-secondary" style="font-size: 0.78rem;">-</span>
                            @endif
                        </td>
                        <td>
                            {{ $tenant->move_in_date ? $tenant->move_in_date->format('F d, Y') : '-' }}
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1 align-items-center table-actions-nowrap">
                                @if(in_array($payCat, ['to_pay', 'partial', 'rejected']) && $tenant->room)
                                    <a href="{{ route('admin.payments.create', ['tenant_id' => $tenant->id]) }}" class="action-btn text-success" title="Record Payment for {{ $tenant->full_name }}">
                                        <i class="bi bi-cash-stack"></i>
                                    </a>
                                @endif
                                @if($tenant->status === 'moved_out')
                                    <button type="button" class="action-btn delete-btn" title="Delete Moved-Out Tenant" 
                                            data-bs-toggle="modal" data-bs-target="#deleteTenantModal{{ $tenant->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @elseif($tenant->status === 'inactive')
                                    <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="action-btn" title="View Tenant Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.tenants.edit', $tenant->id) }}" class="action-btn" title="Edit Tenant">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="action-btn delete-btn" title="Delete Inactive Tenant" 
                                            data-bs-toggle="modal" data-bs-target="#deleteTenantModal{{ $tenant->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @else
                                    <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="action-btn" title="View Tenant Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.tenants.edit', $tenant->id) }}" class="action-btn" title="Edit Tenant">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($tenant->status === 'active')
                                        <button type="button" class="action-btn text-warning" title="Move Out Tenant" 
                                                 data-bs-toggle="modal" data-bs-target="#moveOutModal{{ $tenant->id }}">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                                <h4 class="empty-state-title">No tenant records found.</h4>
                                <p class="empty-state-desc">There are currently no tenant records in the system.</p>
                                <a href="{{ route('admin.tenants.create') }}" class="btn-primary-custom text-decoration-none">
                                    <i class="bi bi-plus-lg"></i> Add Tenant
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tenants->hasPages())
        <div class="p-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-color: var(--border-color) !important;">
            <div class="text-secondary" style="font-size: 0.82rem;">
                Showing {{ $tenants->firstItem() }} to {{ $tenants->lastItem() }} of {{ $tenants->total() }} tenants
            </div>
            <div>
                {{ $tenants->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Modals placed outside table container to prevent stacking context clipping -->
@foreach($tenants as $tenant)
    <!-- Move Out Modal -->
    <div class="modal fade text-start" id="moveOutModal{{ $tenant->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-card p-4 border-0">
                <div class="text-center mb-3">
                    <div class="empty-state-icon mx-auto text-warning mb-2" style="width: 56px; height: 56px; background-color: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                        <i class="bi bi-box-arrow-right fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Move Out Tenant</h5>
                    <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                        Confirm that <strong>{{ $tenant->full_name }}</strong> is officially moving out.
                    </p>
                </div>

                <div class="p-3 rounded mb-3" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color); font-size: 0.85rem;">
                    <div class="fw-bold fs-6 mb-1">{{ $tenant->full_name }}</div>
                    <div class="text-secondary">Tenant Code: <strong class="text-primary font-monospace">{{ $tenant->tenant_code ?? '-' }}</strong></div>
                    <div class="text-secondary">Assigned Room: <strong style="color: var(--text-primary);">{{ $tenant->room ? 'Room ' . $tenant->room->room_number : 'Unassigned' }}</strong></div>
                    <div class="text-secondary">Move-in Date: {{ $tenant->move_in_date ? $tenant->move_in_date->format('M d, Y') : 'N/A' }}</div>
                </div>

                <form action="{{ route('admin.tenants.mark-moved-out', $tenant->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Official Move-out Date <span class="text-danger">*</span></label>
                        <input type="date" name="move_out_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        <small class="text-muted">Frees up the room slot and marks the tenant as <strong>Moved Out</strong>.</small>
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-start gap-2" style="font-size: 0.82rem;">
                        <i class="bi bi-info-circle-fill fs-6 mt-1 flex-shrink-0"></i>
                        <div>Once moved out, the room is released, future dues stop, and the admin will be able to delete this record if needed.</div>
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
    <div class="modal fade text-start" id="blockedDeleteModal{{ $tenant->id }}" tabindex="-1" aria-hidden="true">
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
                    <div class="text-secondary">Tenant Code: {{ $tenant->tenant_code ?? '-' }}</div>
                    <div class="text-secondary">Assigned Room: {{ $tenant->room ? 'Room ' . $tenant->room->room_number : 'Unassigned' }}</div>
                    <div class="text-secondary">Current Status: <span class="badge bg-success-subtle text-success">Active Resident</span></div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Close</button>
                    @if($tenant->status === 'active')
                        <button type="button" class="btn btn-warning text-dark fw-bold px-3" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#moveOutModal{{ $tenant->id }}" style="border-radius: 8px;">
                            <i class="bi bi-box-arrow-right me-1"></i> Move Out Tenant First
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($tenant->status === 'moved_out' || $tenant->status === 'inactive')
        <!-- Delete Tenant Modal for Moved-Out or Inactive Tenant -->
        <div class="modal fade text-start" id="deleteTenantModal{{ $tenant->id }}" tabindex="-1" aria-hidden="true">
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
    @endif
@endforeach
@endsection
