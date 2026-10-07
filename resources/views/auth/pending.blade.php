@extends('layouts.auth')

@section('title', 'Account Pending Approval')
@section('card_width', '600px')

@section('content')
<div class="text-center mb-4">
    <div class="d-inline-flex align-items-center justify-content-center mb-3" 
         style="width: 72px; height: 72px; border-radius: 50%; background-color: #fef3c7; color: #b45309;">
        <i class="bi bi-clock-history fs-1"></i>
    </div>
    <h3 class="fw-bold mb-1" style="font-size: 1.35rem; color: var(--text-primary);">Account Pending Approval</h3>
    <div class="badge-pill badge-warning mt-2 mb-3">
        <i class="bi bi-hourglass-split"></i> Status: PENDING
    </div>
    <p class="text-secondary mb-0" style="font-size: 0.92rem; line-height: 1.5;">
        Your tenant registration has been submitted successfully.<br>
        The Admin must verify your application and assign/confirm your room before you can access the Tenant Dashboard.
    </p>
</div>

<div class="p-3 rounded mb-4" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
    <h6 class="fw-bold mb-3 text-secondary" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">Applicant Summary</h6>
    <div class="row g-2" style="font-size: 0.875rem;">
        <div class="col-sm-4 text-secondary">Applicant Name:</div>
        <div class="col-sm-8 fw-semibold text-truncate">{{ $user->name }}</div>

        <div class="col-sm-4 text-secondary">Email:</div>
        <div class="col-sm-8 fw-semibold text-truncate">{{ $user->email }}</div>

        <div class="col-sm-4 text-secondary">Contact Number:</div>
        <div class="col-sm-8 fw-semibold">{{ $tenant->contact_number ?? '-' }}</div>

        <div class="col-sm-4 text-secondary">Registration Date:</div>
        <div class="col-sm-8 fw-semibold">{{ $user->created_at->format('F d, Y h:i A') }}</div>

        <div class="col-sm-4 text-secondary">Preferred Room:</div>
        <div class="col-sm-8 fw-semibold">
            @if($activeRequest && $activeRequest->room)
                <span class="badge bg-primary text-white">
                    Room {{ $activeRequest->room->room_number }} ({{ $activeRequest->room->room_type }})
                </span>
                <span class="text-secondary d-block mt-1" style="font-size: 0.78rem;">
                    ₱{{ number_format($activeRequest->room->monthly_rent, 2) }}/month • Requested {{ $activeRequest->created_at->diffForHumans() }}
                </span>
            @else
                <span class="text-muted">No preferred room selected yet</span>
            @endif
        </div>
    </div>
</div>

<div class="d-flex flex-column gap-2 mb-4">
    <a href="{{ route('public.rooms') }}" class="btn-primary-custom justify-content-center py-2 text-decoration-none">
        <i class="bi bi-door-open"></i> Browse Available Rooms
    </a>

    <form action="{{ route('logout') }}" method="POST" class="w-100">
        @csrf
        <button type="submit" class="btn-secondary-custom justify-content-center py-2 w-100">
            <i class="bi bi-box-arrow-left"></i> Logout
        </button>
    </form>
</div>

<div class="text-center text-muted" style="font-size: 0.78rem;">
    Need immediate assistance? Please contact the Admin directly at the boarding house office.
</div>
@endsection
