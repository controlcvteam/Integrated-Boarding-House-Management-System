@extends('layouts.auth')

@section('title', 'Sign In')
@section('card_width', '440px')

@section('content')
<div class="mb-4 text-center">
    <h3 class="fw-bold mb-1" style="font-size: 1.15rem; color: var(--text-primary);">Welcome!</h3>
    <p class="text-secondary" style="font-size: 0.85rem;">Sign in to your account to continue</p>
</div>

<form action="{{ route('login') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                <i class="bi bi-envelope"></i>
            </span>
            <input type="email" name="email" id="email" 
                   class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" required autofocus placeholder="name@example.com">
        </div>
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label mb-0">Password <span class="text-danger">*</span></label>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0" style="border-color: var(--input-border); color: var(--text-secondary);">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password" name="password" id="password" 
                   class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                   required placeholder="Enter your password">
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label text-secondary" for="remember" style="font-size: 0.85rem;">
                Remember me
            </label>
        </div>
    </div>

    <button type="submit" class="btn-primary-custom w-100 justify-content-center py-2 fs-6 mb-3">
        <i class="bi bi-box-arrow-in-right"></i> Sign In
    </button>
</form>

<div class="text-center pt-2 border-top" style="border-color: var(--border-color) !important;">
    <p class="text-secondary mb-0" style="font-size: 0.875rem;">
        New tenant? 
        <a href="{{ route('register') }}" class="fw-bold text-decoration-none" style="color: #38bdf8;">
            Register as Tenant
        </a>
    </p>
</div>
@endsection
