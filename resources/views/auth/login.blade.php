@extends('layouts.guest')

@section('title', 'Login - Savora Cafeteria')

@section('content')
<div class="auth-page">

    <!-- left side -->
    <div class="auth-hero d-none d-lg-flex">
        <img src="{{ asset('assets/images/logo-icon.png') }}" alt="Savora" class="logo-icon">
        <div class="brand-title brand-font">Savora</div>
        <div class="brand-sub">CAFETERIA</div>
        <div class="tagline">Good Food &nbsp;·&nbsp; Great Mood</div>
    </div>

    <!-- right side -->
    <div class="auth-form-side">
        <div class="auth-card">

            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="Savora" class="logo-icon">
            <div class="card-brand brand-font">Savora</div>
            <div class="card-brand-sub">CAFETERIA</div>

            <h2 class="h4 mb-1">Welcome Back</h2>
            <p class="subtitle">Log in to your account and enjoy personalized recommendations.</p>

            <form id="loginForm" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-icon-group">
                        <i class="bi bi-envelope icon-left"></i>
                        <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-icon-group">
                        <i class="bi bi-lock icon-left"></i>
                        <input type="password" class="form-control" id="password" placeholder="Enter your password" required>
                        <button type="button" class="icon-toggle">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div id="authErrors" class="auth-errors mb-2"></div>

                <button type="submit" id="loginBtn" class="btn btn-savora w-100 d-flex align-items-center justify-content-center gap-2 mt-2">
                    <span class="btn-label">Login</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </form>

            <div class="auth-links d-flex justify-content-between">
                <span><i class="bi bi-person"></i> Don't have an account? <a href="{{ route('register') }}">Register</a></span>
            </div>
            <div class="mt-2">
                <a href="{{ route('menu') }}" class="text-muted small">
                    <i class="bi bi-eye"></i> Browse the menu as a guest →
                </a>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/auth.js') }}?v={{ filemtime(public_path('assets/js/auth.js')) }}"></script>
@endpush
