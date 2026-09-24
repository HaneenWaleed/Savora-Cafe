@extends('layouts.guest')

@section('title', 'Create Account - Savora Cafeteria')

@section('content')
<div class="auth-page">

    <!-- الجزء الشمال -->
    <div class="auth-hero d-none d-lg-flex">
        <img src="{{ asset('assets/images/logo-icon.png') }}" alt="Savora" class="logo-icon">
        <div class="brand-title brand-font">Savora</div>
        <div class="brand-sub">CAFETERIA</div>
        <div class="tagline">Good Food &nbsp;·&nbsp; Great Mood</div>
    </div>

    <!-- الجزء اليمين -->
    <div class="auth-form-side">
        <div class="auth-card">

            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="Savora" class="logo-icon">
            <div class="card-brand brand-font">Savora</div>
            <div class="card-brand-sub">CAFETERIA</div>

            <h2 class="h4 mb-1">Create Account</h2>
            <p class="subtitle">Join Savora and get recommendations made just for you.</p>

            <form id="registerForm" novalidate>
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-icon-group">
                        <i class="bi bi-person icon-left"></i>
                        <input type="text" class="form-control" id="name" placeholder="Your name" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-icon-group">
                        <i class="bi bi-envelope icon-left"></i>
                        <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-7 mb-3">
                        <label for="phone" class="form-label">Phone <span class="text-muted">(optional)</span></label>
                        <div class="input-icon-group">
                            <i class="bi bi-telephone icon-left"></i>
                            <input type="text" class="form-control" id="phone" placeholder="01xxxxxxxxx">
                        </div>
                    </div>
                    <div class="col-5 mb-3">
                        <label for="age" class="form-label">Age <span class="text-muted">(optional)</span></label>
                        <input type="number" class="form-control" id="age" placeholder="25" style="height:48px; border-radius:10px; border:1px solid #E4DBC9;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-icon-group">
                        <i class="bi bi-lock icon-left"></i>
                        <input type="password" class="form-control" id="password" placeholder="At least 8 characters" required>
                        <button type="button" class="icon-toggle">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="input-icon-group">
                        <i class="bi bi-lock-fill icon-left"></i>
                        <input type="password" class="form-control" id="password_confirmation" placeholder="Re-enter your password" required>
                        <button type="button" class="icon-toggle">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div id="authErrors" class="auth-errors mb-2"></div>

                <button type="submit" id="registerBtn" class="btn btn-savora w-100 d-flex align-items-center justify-content-center gap-2 mt-2">
                    <span class="btn-label">Create Account</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </form>

            <div class="auth-links d-flex justify-content-between">
                <span><i class="bi bi-box-arrow-in-right"></i> Already have an account? <a href="{{ route('login') }}">Login</a></span>
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
