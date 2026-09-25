@extends('layouts.app')

@section('title', 'My Orders - Savora Cafeteria')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/account.css') }}">
@endpush

@section('content')
    <div class="account-shell page-shell cart-page">
        <aside class="account-sidebar reveal">
            <nav class="side-nav">
                <a href="{{ route('profile') }}" class="side-link">
                    <i class="bi bi-person"></i>
                    <span>My Profile</span>
                </a>
                <a href="{{ route('orders') }}" class="side-link active">
                    <i class="bi bi-bag"></i>
                    <span>My Orders</span>
                </a>
                <a href="{{ route('favorites') }}" class="side-link">
                    <i class="bi bi-heart"></i>
                    <span>Favorites</span>
                </a>
                <a href="{{ route('preferences') }}" class="side-link">
                    <i class="bi bi-sliders"></i>
                    <span>Preferences</span>
                </a>
                <a href="#" class="side-link">
                    <i class="bi bi-robot"></i>
                    <span>AI Chatbot</span>
                </a>
            </nav>

            <div class="promo-box">
                <p>Good food brings people together</p>
            </div>
        </aside>

        <main class="account-main reveal cart-main">
            <div class="subpage-topbar">
                <div>
                    <span class="mini-label">Recent activity</span>
                    <h1>My Orders</h1>
                </div>
                <a href="{{ route('menu') }}" class="btn btn-savora">Order again</a>
            </div>

            <div id="ordersList" class="cart-items-panel panel">
                <div class="empty-state">
                    <h3>Loading your orders...</h3>
                </div>
            </div>
        </main>

        <aside class="account-sidecards reveal">
            <div class="right-card summary-card">
                <div class="panel-head small-head">
                    <div class="panel-title-wrap">
                        <i class="bi bi-receipt"></i>
                        <h2>Order Summary</h2>
                    </div>
                </div>

                <div class="summary-row">
                    <span>Placed</span>
                    <strong id="ordersCount">0</strong>
                </div>
                <div class="summary-row">
                    <span>Favorites</span>
                    <strong id="favoriteCount">0</strong>
                </div>
                <div class="summary-row">
                    <span>Cart</span>
                    <strong id="cartCount">0</strong>
                </div>
                <div class="summary-row total-row">
                    <span>Profile</span>
                    <strong id="profileName">Guest</strong>
                </div>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const ordersList = document.getElementById('ordersList');
            const ordersCount = document.getElementById('ordersCount');
            const favoriteCount = document.getElementById('favoriteCount');
            const cartCount = document.getElementById('cartCount');
            const profileName = document.getElementById('profileName');
            const user = getUser();

            if (!user) {
                window.location.href = '/login';
                return;
            }

            const renderOrders = (orders = []) => {
                ordersCount.textContent = orders.length;
                favoriteCount.textContent = '0';
                cartCount.textContent = '0';
                profileName.textContent = user.name || 'Guest';

                if (!orders.length) {
                    ordersList.innerHTML = `
                        <div class="empty-state">
                            <h3>No orders yet</h3>
                            <p>Your recent orders and order history will appear here.</p>
                            <a href="/menu" class="btn btn-savora">Start ordering</a>
                        </div>
                    `;
                    return;
                }

                ordersList.innerHTML = orders.map((order) => {
                    const items = Array.isArray(order.items) ? order.items : [];
                    const firstItem = items[0];
                    const itemName = firstItem?.name || 'Savora order';
                    const image = firstItem?.image_url || 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=400&q=80';
                    const total = Number(order.total_price || 0);
                    const date = order.created_at ? new Date(order.created_at).toLocaleDateString('en-GB', {
                        day: '2-digit', month: 'short', year: 'numeric'
                    }) : 'Recent';
                    const status = (order.status || 'pending').toString();
                    const payment = (order.payment_status || 'pending').toString();

                    return `
                        <div class="cart-item">
                            <div class="cart-image" style="background-image:url('${image}')"></div>
                            <div class="cart-details">
                                <div class="cart-header">
                                    <h3>Order #${order.id}</h3>
                                    <span class="dot-badge ${status.toLowerCase()}">${status}</span>
                                </div>
                                <p>${itemName} · ${items.length} items · Total ${total.toFixed(2)} USD</p>
                                <div class="cart-actions">
                                    <div class="qty-box">
                                        <span>${date}</span>
                                    </div>
                                    <strong>${payment}</strong>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            };

            try {
                const response = await fetch('/api/orders', { headers: authHeaders() });
                if (!response.ok) {
                    throw new Error('Unable to load orders');
                }
                const payload = await response.json();
                const orders = payload.data || [];
                renderOrders(orders);
            } catch (error) {
                console.error(error);
                renderOrders([]);
            }
        });
    </script>
@endpush
