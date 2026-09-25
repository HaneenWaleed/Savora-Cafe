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
        document.addEventListener('DOMContentLoaded', async () => {
            if (!isLoggedIn()) {
                requireLogin();
                return;
            }

            const container = document.getElementById('cartItemsContainer');
            const subtotalEl = document.getElementById('summarySubtotal');
            const deliveryEl = document.getElementById('summaryDelivery');
            const taxEl = document.getElementById('summaryTax');
            const totalEl = document.getElementById('summaryTotal');
            const FALLBACK_IMAGE = 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=400&q=80';

            const getCartItems = async () => {
                try {
                    const response = await fetch(`${API_BASE}/cart`, { headers: authHeaders() });
                    if (!response.ok) return [];
                    const data = await response.json();
                    return Array.isArray(data.items) ? data.items : [];
                } catch (error) {
                    console.error('Failed to load cart:', error);
                    return [];
                }
            };

            const updateItemQuantity = async (cartItemId, quantity) => {
                const response = await fetch(`${API_BASE}/cart/items/${cartItemId}`, {
                    method: 'PATCH',
                    headers: authHeaders(),
                    body: JSON.stringify({ quantity }),
                });
                if (!response.ok) {
                    const data = await response.json().catch(() => ({}));
                    throw new Error(data.message || 'Failed to update quantity');
                }
            };

            const removeCartItem = async (cartItemId) => {
                const response = await fetch(`${API_BASE}/cart/items/${cartItemId}`, {
                    method: 'DELETE',
                    headers: authHeaders(false),
                });
                if (!response.ok) throw new Error('Failed to remove item');
            };

            const renderCartState = (items = []) => {
                const subtotal = items.reduce((sum, item) => sum + Number(item.line_total || 0), 0);
                const delivery = subtotal > 0 ? 25 : 0;
                const tax = subtotal > 0 ? subtotal * 0.05 : 0;
                const total = subtotal + delivery + tax;

                subtotalEl.textContent = `${subtotal.toFixed(2)} EGP`;
                deliveryEl.textContent = `${delivery.toFixed(2)} EGP`;
                taxEl.textContent = `${tax.toFixed(2)} EGP`;
                totalEl.textContent = `${total.toFixed(2)} EGP`;

                if (!items.length) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <h3>Your cart is empty</h3>
                            <p>Start adding dishes you love and they'll appear here.</p>
                            <a href="/menu" class="btn btn-savora">Browse menu</a>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = items.map((item) => {
                    const product = item.product || {};
                    const quantity = Number(item.quantity || 1);
                    const subtotalValue = Number(item.line_total || 0);
                    const image = product.image_url || FALLBACK_IMAGE;

                    return `
                        <div class="cart-item" data-cart-id="${item.id}">
                            <div class="cart-image" style="background-image:url('${image}')"></div>
                            <div class="cart-details">
                                <div class="cart-header">
                                    <h3>${product.name || 'Cart item'}</h3>
                                    <button type="button" class="delete-btn" data-cart-id="${item.id}" aria-label="Remove item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <p>${product.description || 'Item in your cart.'}</p>
                                <div class="cart-actions">
                                    <div class="qty-box">
                                        <button type="button" class="qty-btn qty-decrease" data-cart-id="${item.id}" data-quantity="${quantity - 1}" ${quantity <= 1 ? 'disabled' : ''}>-</button>
                                        <span>${quantity}</span>
                                        <button type="button" class="qty-btn qty-increase" data-cart-id="${item.id}" data-quantity="${quantity + 1}">+</button>
                                    </div>
                                    <strong>${subtotalValue.toFixed(2)} EGP</strong>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                container.querySelectorAll('.qty-btn').forEach((button) => {
                    button.addEventListener('click', async () => {
                        try {
                            await updateItemQuantity(button.dataset.cartId, Number(button.dataset.quantity));
                            renderCartState(await getCartItems());
                        } catch (error) {
                            console.error(error);
                            showToast('Could not update cart.');
                        }
                    });
                });

                container.querySelectorAll('.delete-btn').forEach((button) => {
                    button.addEventListener('click', async () => {
                        try {
                            await removeCartItem(button.dataset.cartId);
                            renderCartState(await getCartItems());
                            showToast('Item removed from cart');
                        } catch (error) {
                            console.error(error);
                            showToast('Could not remove item.');
                        }
                    });
                });
            };

            renderCartState(await getCartItems());

            document.querySelector('.checkout-btn')?.addEventListener('click', async () => {
                const response = await fetch(`${API_BASE}/orders`, {
                    method: 'POST',
                    headers: authHeaders(),
                    body: JSON.stringify({}),
                });
                if (response.ok) window.location.href = '/orders';
                else showToast('Unable to place the order. Please review your cart.');
            });
        });
    </script>
@endpush
