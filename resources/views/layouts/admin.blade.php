@extends('layouts.app')

@section('body_class', 'admin-body')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">
@endpush

@section('content')
    <div class="admin-dashboard-shell">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-brand-block">
                <a href="{{ route('admin.dashboard') }}" class="admin-brand-link">
                    <img src="{{ asset('assets/images/logo-icon.png') }}" alt="Savora">
                    <div class="admin-brand-copy"><strong>Savora</strong><small>CAFETERIA</small></div>
                </a>
            </div>
            <div class="admin-profile-block"><div class="admin-user-avatar" id="adminAvatar">A</div><div><strong id="adminName">Admin</strong><small id="adminRole">Administrator</small></div></div>
            <nav class="admin-sidebar-nav" aria-label="Admin navigation">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
                <a href="{{ route('admin.users') }}" class="admin-nav-item {{ request()->routeIs('admin.users') ? 'is-active' : '' }}"><i class="bi bi-people"></i><span>Users</span></a>
                <a href="{{ route('admin.food-items') }}" class="admin-nav-item {{ request()->routeIs('admin.food-items') ? 'is-active' : '' }}"><i class="bi bi-journal-text"></i><span>Food Items</span></a>
                <a href="{{ route('admin.beverages') }}" class="admin-nav-item {{ request()->routeIs('admin.beverages') ? 'is-active' : '' }}"><i class="bi bi-cup-straw"></i><span>Beverages</span></a>
                <a href="{{ route('admin.categories') }}" class="admin-nav-item {{ request()->routeIs('admin.categories') ? 'is-active' : '' }}"><i class="bi bi-tags"></i><span>Categories</span></a>
                <a href="{{ route('admin.orders') }}" class="admin-nav-item {{ request()->routeIs('admin.orders') ? 'is-active' : '' }}"><i class="bi bi-bag-check"></i><span>Orders</span></a>
                <a href="{{ route('admin.customers') }}" class="admin-nav-item {{ request()->routeIs('admin.customers') ? 'is-active' : '' }}"><i class="bi bi-person-lines-fill"></i><span>Customers</span></a>
                <a href="{{ route('admin.statistics') }}" class="admin-nav-item {{ request()->routeIs('admin.statistics') ? 'is-active' : '' }}"><i class="bi bi-bar-chart-line"></i><span>Statistics</span></a>
                <a href="{{ route('admin.ai') }}" class="admin-nav-item {{ request()->routeIs('admin.ai') ? 'is-active' : '' }}"><i class="bi bi-robot"></i><span>AI Assistant</span></a>
            </nav>
            <button type="button" class="admin-sidebar-collapse" id="adminCollapse"><i class="bi bi-layout-sidebar-inset"></i><span>Collapse sidebar</span></button>
        </aside>
        <main class="admin-main-panel">
            <header class="admin-topbar"><button type="button" class="admin-mobile-menu" id="adminMobileMenu" aria-label="Open menu"><i class="bi bi-list"></i></button><div class="admin-searchbox"><i class="bi bi-search"></i><input id="adminGlobalSearch" type="search" placeholder="Search this page..." aria-label="Search"></div><div class="admin-topbar-actions"><span class="admin-date"><i class="bi bi-calendar3"></i> <span id="adminToday"></span></span><button type="button" class="admin-icon-button" id="adminLogout" aria-label="Log out"><i class="bi bi-box-arrow-right"></i></button></div></header>
            <div class="admin-content-wrap">@yield('admin_content')</div>
        </main>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/admin-dashboard.js') }}?v={{ filemtime(public_path('assets/js/admin-dashboard.js')) }}"></script>
@endpush
