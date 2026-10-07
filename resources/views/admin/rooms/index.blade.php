@extends('layouts.admin')

@section('title', 'Rooms')
@section('page_title', 'Rooms')
@section('page_subtitle', 'Manage all room records')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <form action="{{ route('admin.rooms.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 flex-grow-1 filter-form-mobile" style="max-width: 700px;">
        <div class="input-group flex-grow-1" style="min-width: 200px; max-width: 320px;">
            <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                   placeholder="Search room # or type..." value="{{ request('search') }}">
        </div>

        <select name="status" class="form-select flex-grow-1 flex-sm-grow-0" style="width: auto;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
            <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied / Full</option>
            <option value="manual_closed" {{ request('status') == 'manual_closed' ? 'selected' : '' }}>Manually Closed</option>
        </select>

        @if($roomTypes->isNotEmpty())
            <select name="room_type" class="form-select flex-grow-1 flex-sm-grow-0" style="width: auto;" onchange="this.form.submit()">
                <option value="">All Types</option>
                @foreach($roomTypes as $type)
                    <option value="{{ $type }}" {{ request('room_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        @endif

        @if(request()->anyFilled(['search', 'status', 'room_type']))
            <a href="{{ route('admin.rooms.index') }}" class="btn btn-sm btn-link text-secondary text-decoration-none">
                <i class="bi bi-x-circle"></i> Clear
            </a>
        @endif
    </form>

    <div>
        <a href="{{ route('admin.rooms.create') }}" class="btn-primary-custom text-decoration-none d-inline-flex align-items-center justify-content-center w-100">
            <i class="bi bi-plus-lg"></i> Add Room
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
                    <th>Room No.</th>
                    <th>Room Type</th>
                    <th>Capacity</th>
                    <th>Rental Fee</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $room)
                    <tr>
                        <td class="fw-bold" style="color: var(--text-primary);">
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center rounded" 
                                      style="width: 32px; height: 32px; background: var(--table-row-hover); font-size: 0.8rem;">
                                    <i class="bi bi-door-closed"></i>
                                </span>
                                <div>
                                    Room {{ $room->room_number }}
                                    @if($room->floor)
                                        <div class="text-secondary fw-normal" style="font-size: 0.75rem;">{{ $room->floor }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $room->room_type }}</td>
                        <td>
                            <span>{{ $room->current_occupancy }} / {{ $room->capacity }}</span>
                            <span class="text-secondary ms-1" style="font-size: 0.78rem;">
                                ({{ $room->available_slots }} slot{{ $room->available_slots == 1 ? '' : 's' }} left)
                            </span>
                        </td>
                        <td class="fw-semibold">₱{{ number_format($room->monthly_rent, 2) }}</td>
                        <td>
                            @if($room->is_available)
                                <span class="badge-pill badge-success">
                                    <i class="bi bi-check-lg"></i> Available
                                </span>
                            @else
                                <span class="badge-pill badge-danger">
                                    <i class="bi bi-x-lg"></i> {{ $room->status_reason }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1 align-items-center table-actions-nowrap">
                                <a href="{{ route('admin.rooms.show', $room->id) }}" class="action-btn" title="View Room Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.rooms.edit', $room->id) }}" class="action-btn" title="Edit Room">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="action-btn delete-btn" title="Delete Room" 
                                        data-bs-toggle="modal" data-bs-target="#deleteRoomModal{{ $room->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="bi bi-door-closed fs-1"></i>
                                </div>
                                <h4 class="empty-state-title">No room records found.</h4>
                                <p class="empty-state-desc">There are currently no rooms matching your filter or search criteria.</p>
                                <a href="{{ route('admin.rooms.create') }}" class="btn-primary-custom text-decoration-none">
                                    <i class="bi bi-plus-lg"></i> Add New Room
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rooms->hasPages())
        <div class="p-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-color: var(--border-color) !important;">
            <div class="text-secondary" style="font-size: 0.82rem;">
                Showing {{ $rooms->firstItem() }} to {{ $rooms->lastItem() }} of {{ $rooms->total() }} rooms
            </div>
            <div>
                {{ $rooms->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Delete Room Modals placed outside table container to prevent stacking context clipping -->
@foreach($rooms as $room)
    <div class="modal fade text-start" id="deleteRoomModal{{ $room->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-card p-4 border-0">
                <div class="text-center mb-3">
                    <div class="empty-state-icon mx-auto text-danger mb-2" style="width: 56px; height: 56px;">
                        <i class="bi bi-trash fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Delete Room</h5>
                    <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                        Are you sure you want to delete <strong>Room {{ $room->room_number }}</strong>? This action cannot be undone.
                    </p>
                </div>

                @if($room->current_occupancy > 0)
                    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 0.82rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        This room currently has {{ $room->current_occupancy }} active occupant(s). You must reassign occupants before deleting.
                    </div>
                @endif

                <div class="d-flex justify-content-end gap-2 mt-2">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-custom" {{ $room->current_occupancy > 0 ? 'disabled' : '' }}>
                            Yes, Delete Room
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
