@extends('layouts.app')

@section('title', 'Cart - Savora Cafeteria')

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
                <a href="{{ route('orders') }}" class="side-link">
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
                <a href="#" class="side-link" data-open-chatbot>
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
                    <span class="mini-label">Your order</span>
                    <h1>Cart</h1>
                </div>
                <a href="{{ route('menu') }}" class="btn btn-savora">Continue Shopping</a>
            </div>

            <div id="cartItemsContainer" class="cart-items-panel panel">
                <div class="empty-state">
                    <h3>Loading your cart...</h3>
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
                    <span>Subtotal</span>
                    <strong id="summarySubtotal">0 EGP</strong>
                </div>
                <div class="summary-row">
                    <span>Delivery</span>
                    <strong id="summaryDelivery">0 EGP</strong>
                </div>
                <div class="summary-row">
                    <span>Tax</span>
                    <strong id="summaryTax">0 EGP</strong>
                </div>
                <div class="summary-row total-row">
                    <span>Total</span>
                    <strong id="summaryTotal">0 EGP</strong>
                </div>

                <button type="button" class="checkout-btn">Proceed to Checkout</button>
            </div>

            <div class="right-card help-card">
                <div class="help-bot">
                    <i class="bi bi-robot"></i>
                </div>
                <h3>Need Help?</h3>
                <p>Chat with our AI for meal suggestions based on your cart.</p>
                <button type="button" class="chat-btn">Chat with AI <i class="bi bi-arrow-right"></i></button>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        const API_BASE = '/api';

        function getToken() {
            return localStorage.getItem('savora_token') || '';
        }

        function authHeaders(json = true) {
            const headers = { Accept: 'application/json' };
            if (json) headers['Content-Type'] = 'application/json';
            if (getToken()) headers['Authorization'] = `Bearer ${getToken()}`;
            return headers;
        }

        function showToast(message) {
            let toast = document.querySelector('.savora-toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.className = 'savora-toast';
                document.body.appendChild(toast);
            }
            toast.textContent = message;
            requestAnimationFrame(() => toast.classList.add('show'));
            setTimeout(() => toast.classList.remove('show'), 2500);
        }

        document.addEventListener('DOMContentLoaded', async () => {
            const container = document.getElementById('cartItemsContainer');
            const subtotalEl = document.getElementById('summarySubtotal');
            const deliveryEl = document.getElementById('summaryDelivery');
            const taxEl = document.getElementById('summaryTax');
            const totalEl = document.getElementById('summaryTotal');

            const getCartItems = async () => {
                try {
                    const response = await fetch('/api/cart', { headers: authHeaders() });
                    const data = response.ok ? await response.json() : { data: [] };
                    return Array.isArray(data.data) ? data.data : [];
                } catch (error) {
                    console.error('Failed to load cart:', error);
                    return [];
                }
            };

            const renderCartState = (items = []) => {
                const subtotal = Number(items.reduce((sum, item) => {
                    const product = item.product || item;
                    const price = Number(product.price || 0);
                    const quantity = Number(item.quantity || 1);
                    return sum + (price * quantity);
                }, 0).toFixed(2));
                const response = await fetch('/api/cart', { headers: authHeaders(false) });
                if (!response.ok) throw new Error('Unable to load cart');
                const payload = await response.json();
                return payload.items || [];
            };

            const renderCartState = (items = []) => {
                const subtotal = Number(items.reduce((sum, item) => sum + Number(item.line_total || 0), 0).toFixed(2));
                const delivery = subtotal > 0 ? 25 : 0;
                const tax = subtotal > 0 ? Number((subtotal * 0.05).toFixed(2)) : 0;
                const total = subtotal + delivery + tax;

                subtotalEl.textContent = `${subtotal.toFixed(2)} EGP`;
                deliveryEl.textContent = `${delivery.toFixed(2)} EGP`;
                taxEl.textContent = `${tax.toFixed(2)} EGP`;
                totalEl.textContent = `${total.toFixed(2)} EGP`;

                if (!items.length) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <h3>Your cart is empty</h3>
                            <p>Start adding dishes you love and they’ll appear here.</p>
                            <a href="/menu" class="btn btn-savora">Browse menu</a>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = items.map((item) => {
                    const product = item.product || item;
                    const price = Number(product.price || 0);
                    const quantity = Number(item.quantity || 1);
                    const subtotalValue = price * quantity;
                    const image = product.image
                        ? `/storage/${String(product.image).replace(/^\//, '')}`
                    const product = item.product || {};
                    const price = Number(item.unit_price || 0);
                    const quantity = Number(item.quantity || 1);
                    const subtotalValue = Number(item.line_total || 0);
                    const image = product.image_url
                        ? product.image_url
                        : 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=400&q=80';
                    const itemType = item.purchasable_type || product.type || 'food';
                    const itemId = item.purchasable_id || product.id || item.id;

                    return `
                        <div class="cart-item" data-item-id="${itemId}" data-item-type="${itemType}">
                            <div class="cart-image" style="background-image:url('${image}')"></div>
                            <div class="cart-details">
                                <div class="cart-header">
                                    <h3>${product.name || 'Cart item'}</h3>
                                    <button type="button" class="delete-btn" data-id="${itemId}" data-type="${itemType}" aria-label="Remove item">
                                        <h3>${product.name || 'Cart item'}</h3>
                                            <button type="button" class="delete-btn" data-id="${item.id}" aria-label="Remove item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <p>${product.description || 'Item in your cart.'}</p>
                                <div class="cart-actions">
                                    <div class="qty-box">
                                        <button type="button" class="qty-btn qty-decrease" data-id="${itemId}" data-type="${itemType}" ${quantity <= 1 ? 'disabled' : ''}>-</button>
                                        <span>${quantity}</span>
                                        <button type="button" class="qty-btn qty-increase" data-id="${itemId}" data-type="${itemType}">+</button>
                                        <button type="button" class="qty-btn qty-decrease" data-id="${item.id}" data-quantity="${quantity - 1}" ${quantity <= 1 ? 'disabled' : ''}>-</button>
                                        <span>${quantity}</span>
                                        <button type="button" class="qty-btn qty-increase" data-id="${item.id}" data-quantity="${quantity + 1}">+</button>
                                    </div>
                                    <strong>${subtotalValue.toFixed(2)} EGP</strong>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                container.querySelectorAll('.qty-btn').forEach((button) => {
                    button.addEventListener('click', async () => {
                        const id = Number(button.dataset.id);
                        const type = button.dataset.type;
                        const delta = button.classList.contains('qty-increase') ? 1 : -1;
                        const currentItem = items.find((entry) =>
                            (entry.purchasable_id === id || entry.product?.id === id) &&
                            (entry.purchasable_type === type || entry.product?.type === type)
                        );

                        if (!currentItem) return;

                        const nextQuantity = Number(currentItem.quantity || 1) + delta;
                        if (nextQuantity < 1) return;

                        try {
                            const response = await fetch(`/api/cart/items/${currentItem.id}`, {
                                method: 'PATCH',
                                headers: authHeaders(),
                                body: JSON.stringify({ quantity: nextQuantity }),
                            });
                            if (!response.ok) throw new Error('Failed to update quantity');
                            const newItems = await getCartItems();
                            renderCartState(newItems);
                        } catch (error) {
                            console.error('Failed to update quantity:', error);
                            showToast('Could not update cart.');
                        }
                        const response = await fetch(`/api/cart/items/${button.dataset.id}`, {
                            method: 'PATCH', headers: authHeaders(),
                            body: JSON.stringify({ quantity: Number(button.dataset.quantity) }),
                        });
                        if (response.ok) renderCartState((await response.json()).items || []);
                    });
                });

                container.querySelectorAll('.delete-btn').forEach((button) => {
                    button.addEventListener('click', async () => {
                        const id = Number(button.dataset.id);
                        const type = button.dataset.type;
                        const currentItem = items.find((entry) =>
                            (entry.purchasable_id === id || entry.product?.id === id) &&
                            (entry.purchasable_type === type || entry.product?.type === type)
                        );

                        if (!currentItem) return;

                        try {
                            const response = await fetch(`/api/cart/items/${currentItem.id}`, {
                                method: 'DELETE',
                                headers: authHeaders(),
                            });
                            if (!response.ok) throw new Error('Failed to remove item');
                            const newItems = await getCartItems();
                            renderCartState(newItems);
                            showToast('Item removed from cart');
                        } catch (error) {
                            console.error('Failed to remove item:', error);
                            showToast('Could not remove item.');
                        }
                        const response = await fetch(`/api/cart/items/${button.dataset.id}`, { method: 'DELETE', headers: authHeaders(false) });
                        if (response.ok) renderCartState((await response.json()).items || []);
                    });
                });
            };

            try {
                const items = await getCartItems();
                renderCartState(items);
                renderCartState(await getCartItems());
            } catch (error) {
                renderCartState([]);
            }

            document.querySelector('.checkout-btn')?.addEventListener('click', async () => {
                const response = await fetch('/api/orders', { method: 'POST', headers: authHeaders(), body: JSON.stringify({}) });
                if (response.ok) window.location.href = '/orders';
                else showToast('Unable to place the order. Please review your cart.');
            });
        });
    </script>
@endpush
