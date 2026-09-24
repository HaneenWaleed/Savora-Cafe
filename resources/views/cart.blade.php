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
                <a href="#" class="side-link">
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
            const container = document.getElementById('cartItemsContainer');
            const subtotalEl = document.getElementById('summarySubtotal');
            const deliveryEl = document.getElementById('summaryDelivery');
            const taxEl = document.getElementById('summaryTax');
            const totalEl = document.getElementById('summaryTotal');

            const getCartItems = () => {
                const store = window.SavoraMockStore;
                if (!store) return [];

                return store.getCart().map((item) => ({
                    id: Number(item.id),
                    type: item.type,
                    quantity: Number(item.quantity || 1),
                    price: Number(item.price || 0),
                    name: item.name,
                    image: item.image,
                    description: item.name || 'Item in your cart.',
                }));
            };

            const renderCartState = (items = []) => {
                const subtotal = Number(items.reduce((sum, item) => sum + ((Number(item.price) || 0) * (Number(item.quantity) || 1)), 0).toFixed(2));
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
                    const price = Number(item.price || 0);
                    const quantity = Number(item.quantity || 1);
                    const subtotalValue = price * quantity;
                    const image = item.image
                        ? `/storage/${String(item.image).replace(/^\//, '')}`
                        : 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=400&q=80';

                    return `
                        <div class="cart-item" data-item-id="${item.id}" data-item-type="${item.type}">
                            <div class="cart-image" style="background-image:url('${image}')"></div>
                            <div class="cart-details">
                                <div class="cart-header">
                                    <h3>${item.name || 'Cart item'}</h3>
                                    <button type="button" class="delete-btn" data-id="${item.id}" data-type="${item.type}" aria-label="Remove item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <p>${item.description || 'Item in your cart.'}</p>
                                <div class="cart-actions">
                                    <div class="qty-box">
                                        <button type="button" class="qty-btn qty-decrease" data-id="${item.id}" data-type="${item.type}" ${quantity <= 1 ? 'disabled' : ''}>-</button>
                                        <span>${quantity}</span>
                                        <button type="button" class="qty-btn qty-increase" data-id="${item.id}" data-type="${item.type}">+</button>
                                    </div>
                                    <strong>${subtotalValue.toFixed(2)} EGP</strong>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                container.querySelectorAll('.qty-btn').forEach((button) => {
                    button.addEventListener('click', () => {
                        const store = window.SavoraMockStore;
                        if (!store) return;

                        const id = Number(button.dataset.id);
                        const type = button.dataset.type;
                        const delta = button.classList.contains('qty-increase') ? 1 : -1;
                        const currentItem = store.getCart().find((entry) => entry.type === type && Number(entry.id) === id);

                        if (!currentItem) return;

                        const nextQuantity = Number(currentItem.quantity || 1) + delta;
                        if (nextQuantity < 1) {
                            return;
                        }

                        store.updateCartQuantity(type, id, delta);
                        renderCartState(getCartItems());
                    });
                });

                container.querySelectorAll('.delete-btn').forEach((button) => {
                    button.addEventListener('click', () => {
                        const store = window.SavoraMockStore;
                        if (!store) return;

                        store.removeCartItem(button.dataset.type, Number(button.dataset.id));
                        renderCartState(getCartItems());
                    });
                });
            };

            try {
                renderCartState(getCartItems());
            } catch (error) {
                renderCartState([]);
            }
        });
    </script>
@endpush
