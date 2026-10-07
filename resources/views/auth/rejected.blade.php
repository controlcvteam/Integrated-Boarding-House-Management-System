@extends('layouts.auth')

@section('title', 'Account Not Approved')
@section('card_width', '550px')

@section('content')
<div class="text-center mb-4">
    <div class="d-inline-flex align-items-center justify-content-center mb-3" 
         style="width: 72px; height: 72px; border-radius: 50%; background-color: #fee2e2; color: #b91c1c;">
        <i class="bi bi-x-circle fs-1"></i>
    </div>
    <h3 class="fw-bold mb-1" style="font-size: 1.35rem; color: var(--text-primary);">Account Not Approved</h3>
    <div class="badge-pill badge-danger mt-2 mb-3">
        <i class="bi bi-slash-circle"></i> Status: REJECTED
    </div>
    <p class="text-secondary mb-0" style="font-size: 0.92rem; line-height: 1.5;">
        Your tenant registration could not be approved at this time.
    </p>
</div>

<div class="p-3 rounded mb-4" style="background-color: var(--table-header-bg); border: 1px solid var(--border-color);">
    <h6 class="fw-bold mb-2 text-danger" style="font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.05em;">
        <i class="bi bi-info-circle me-1"></i> Reason Provided by Admin:
    </h6>
    <div class="p-2 rounded bg-body" style="font-size: 0.88rem; color: var(--text-primary); border: 1px solid var(--border-color);">
        {{ $user->rejection_reason ?: 'No specific reason provided. Please contact the Admin for details.' }}
    </div>
</div>

<form action="{{ route('logout') }}" method="POST" class="w-100">
    @csrf
    <button type="submit" class="btn-primary-custom justify-content-center py-2 w-100 mb-3">
        <i class="bi bi-box-arrow-left"></i> Back to Login
    </button>
</form>

<div class="text-center text-muted" style="font-size: 0.78rem;">
    If you believe this is a mistake, you may visit the boarding house or contact the Admin.
</div>
@endsection
