@extends('layouts.app')

@section('title', 'Favorites - Savora Cafeteria')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/account.css') }}">
@endpush

@section('content')
    <div class="account-shell page-shell favorites-page">
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
                <a href="{{ route('favorites') }}" class="side-link active">
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

        <main class="account-main reveal favorites-main">
            <div class="subpage-topbar">
                <div>
                    <span class="mini-label">Your saved picks</span>
                    <h1>Favorites</h1>
                </div>
                <a href="{{ route('menu') }}" class="btn btn-savora">Browse Menu</a>
            </div>

            <div id="favoritesGrid" class="favorites-grid">
                <div class="panel empty-state">
                    <h3>Loading favorites...</h3>
                </div>
            </div>
        </main>

        <aside class="account-sidecards reveal">
            <div class="right-card recent-orders no-border">
                <div class="panel-head small-head">
                    <div class="panel-title-wrap">
                        <i class="bi bi-stars"></i>
                        <h2>Recommended</h2>
                    </div>
                    <a href="{{ route('menu') }}" class="mini-link">View All</a>
                </div>

                <ul id="recommendedList" class="recent-list compact-list">
                    <li><div class="empty-state"><p>Loading recommendations...</p></div></li>
                </ul>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const favoritesGrid = document.getElementById('favoritesGrid');
            const recommendedList = document.getElementById('recommendedList');

            const resolveFavoriteProduct = (item = {}) => {
                const source = item.product || item.item || item;
                const productType = source?.type || item?.type || 'food';
                const productId = Number(source?.id ?? item?.id ?? 0);
                const store = window.SavoraMockStore;
                const productFromStore = store && productType && productId ? store.findProduct(productType, productId) : null;
                const merged = {
                    ...(productFromStore || {}),
                    ...(source || {}),
                    ...(item || {}),
                };

                return {
                    ...merged,
                    id: Number(merged.id ?? productId ?? 0),
                    type: productType,
                    name: merged.name || source?.name || 'Saved item',
                    price: Number(merged.price ?? source?.price ?? 0),
                    image: merged.image || merged.image_url || source?.image || source?.image_url || item?.image || item?.image_url || 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=800&q=80',
                    description: merged.description || source?.description || 'Your saved food favorite.',
                    category: merged.category || source?.category || { name: 'Favorite' },
                };
            };

            const renderFavorites = (items = []) => {
                if (!items.length) {
                    favoritesGrid.innerHTML = `
                        <div class="panel empty-state">
                            <h3>No favorites yet</h3>
                            <p>Your saved dishes and drinks will appear here once you add them.</p>
                            <a href="/menu" class="btn btn-savora">Explore the menu</a>
                        </div>
                    `;
                    recommendedList.innerHTML = `
                        <li class="empty-state">
                            <p>We’ll suggest dishes here as soon as your favorite items are available.</p>
                        </li>
                    `;
                    return;
                }

                favoritesGrid.innerHTML = items.map((item) => {
                    const product = resolveFavoriteProduct(item);
                    const image = product.image || 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=800&q=80';

                    return `
                        <article class="favorite-card">
                            <div class="favorite-image" style="background-image:url('${image}')"></div>
                            <div class="favorite-body">
                                <div class="fav-head">
                                    <h3>${product.name || 'Saved item'}</h3>
                                    <button type="button" class="favorite-heart active" data-product-type="${product.type}" data-product-id="${product.id}" aria-label="Remove from favorites">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                </div>
                                <div class="food-meta">
                                    <span>${product.category?.name || 'Favorite'}</span>
                                    <strong>${Number(product.price || 0)} EGP</strong>
                                </div>
                                <p>${product.description || 'Your saved food favorite.'}</p>
                            </div>
                        </article>
                    `;
                }).join('');

                recommendedList.innerHTML = items.slice(0, 3).map((item) => {
                    const product = resolveFavoriteProduct(item);
                    const image = product.image || 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=400&q=80';

                    return `
                        <li>
                            <img src="${image}" alt="${product.name || 'Favorite item'}">
                            <div>
                                <strong>${product.name || 'Favorite item'}</strong>
                                <small>${Number(product.price || 0)} EGP</small>
                                <span>Based on your tastes</span>
                            </div>
                        </li>
                    `;
                }).join('');

                favoritesGrid.querySelectorAll('.favorite-heart').forEach((button) => {
                    button.addEventListener('click', () => {
                        const type = button.dataset.productType;
                        const id = Number(button.dataset.productId || 0);
                        const store = window.SavoraMockStore;

                        if (!store || !type || !id) {
                            return;
                        }

                        store.toggleFavorite(type, id);
                        const nextFavorites = store.getFavorites();
                        renderFavorites(nextFavorites);
                        showToast('Removed from favorites.');
                    });
                });
            };

            const syncFavoritesView = async () => {
                try {
                    const dashboard = await fetchDashboardData();
                    renderFavorites(dashboard.authenticated ? dashboard.favorites : []);
                } catch (error) {
                    renderFavorites([]);
                }
            };

            window.addEventListener('savora:favorites-updated', syncFavoritesView);
            await syncFavoritesView();
        });
    </script>
@endpush
