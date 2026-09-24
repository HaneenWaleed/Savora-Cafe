@php
    $ingredients = collect($item->ingredients ?? [])->map(fn ($value) => trim((string) $value))->filter()->values()->all();
    $spiceLevel = max(0, min(5, (int) ($item->spicy_level ?? 0)));
    $categoryName = $item->category?->name ?? (($type === 'food') ? 'Food item' : 'Drink');
    $image = $item->image
        ? asset('storage/' . $item->image)
        : (($type === 'food')
            ? 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=1200&q=80'
            : 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80');
    $gallery = collect([$image, $image, $image, $image])->take(4)->values()->all();
    $prepTime = (int) ($item->preparation_time ?? 15);
    $isVegetarian = str_contains(strtolower($categoryName), 'vegetarian') || collect($ingredients)->contains(fn ($ingredient) => str_contains(strtolower((string) $ingredient), 'basil'));
    $heatLabel = $spiceLevel >= 3 ? 'Hot' : ($spiceLevel >= 1 ? 'Mild' : 'Smooth');
    $temperatureLabel = ($type === 'beverage') ? (($item->temperature ?? 'Hot') === 'cold' ? 'Cold' : 'Hot') : 'Hot';
    $currentPrice = number_format((float) $item->price, 2);
    $currentCalories = (int) ($item->calories ?? 0);
@endphp

@extends('layouts.app')

@section('title', $item->name . ' - Savora Cafeteria')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/product-detail.css') }}">
@endpush

@section('content')
    <div class="product-page">
        <nav class="product-breadcrumb reveal" aria-label="Breadcrumb">
            <a href="{{ route('menu') }}"><i class="bi bi-house-door"></i> Home</a>
            <span>/</span>
            <a href="{{ route('menu') }}">Menu</a>
            <span>/</span>
            <span>{{ $categoryName }}</span>
            <span>/</span>
            <span class="current-page">{{ $item->name }}</span>
        </nav>

        <div class="product-shell reveal">
            <section class="product-gallery">
                <button type="button" class="wishlist-btn" id="productFavoriteBtn" aria-label="Add to favorites">
                    <i class="bi bi-heart"></i>
                </button>

                <div class="gallery-main">
                    <img src="{{ $image }}" alt="{{ $item->name }}" id="mainProductImage">
                </div>

                <div class="gallery-thumbs" aria-label="Item gallery">
                    @foreach ($gallery as $thumb)
                        <button type="button" class="gallery-thumb {{ $loop->first ? 'active' : '' }}" data-image="{{ $thumb }}">
                            <img src="{{ $thumb }}" alt="{{ $item->name }} thumbnail {{ $loop->iteration }}">
                        </button>
                    @endforeach
                </div>
            </section>

            <section class="product-info">
                <div class="product-header-row">
                    <div>
                        <h1>{{ $item->name }}</h1>
                        <div class="product-subtitle">
                            <span class="category-pill">{{ $categoryName }}</span>
                        </div>
                    </div>
                    <span class="match-pill"><i class="bi bi-stars"></i> 96% match</span>
                </div>

                <div class="price-row">
                    <div class="product-price">{{ number_format((float) $item->price, 2) }} EGP</div>
                    <div class="product-tags">
                        <span class="tag tag-terracotta"><i class="bi bi-fire"></i> {{ $heatLabel }}</span>
                        @if($isVegetarian)
                            <span class="tag tag-green"><i class="bi bi-leaf"></i> Vegetarian</span>
                        @endif
                        <span class="tag tag-warm"><i class="bi bi-sun"></i> {{ $temperatureLabel }}</span>
                    </div>
                </div>

                <p class="product-description">
                    {{ $item->description ?: 'A signature Savora favorite made with fresh ingredients, balanced seasoning, and rich flavor that feels comforting and premium.' }}
                </p>

                <div class="info-grid">
                    <div class="info-card">
                        <span class="info-icon"><i class="bi bi-fire"></i></span>
                        <div>
                            <small>Calories</small>
                            <strong>{{ (int) ($item->calories ?? 0) }} kcal</strong>
                        </div>
                    </div>
                    <div class="info-card">
                        <span class="info-icon"><i class="bi bi-thermometer-half"></i></span>
                        <div>
                            <small>Temp</small>
                            <strong>{{ $temperatureLabel }}</strong>
                        </div>
                    </div>
                    <div class="info-card">
                        <span class="info-icon"><i class="bi bi-clock"></i></span>
                        <div>
                            <small>Prep Time</small>
                            <strong>{{ $prepTime }} min</strong>
                        </div>
                    </div>
                    <div class="info-card">
                        <span class="info-icon"><i class="bi bi-rulers"></i></span>
                        <div>
                            <small>Size</small>
                            <strong>{{ $type === 'beverage' ? 'Medium' : 'Large' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Ingredients</h3>
                    <div class="chip-list">
                        @forelse ($ingredients as $ingredient)
                            <span class="chip">{{ $ingredient }}</span>
                        @empty
                            <span class="chip">Fresh ingredients</span>
                            <span class="chip">Savora sauce</span>
                            <span class="chip">Premium herbs</span>
                        @endforelse
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Spiciness level</h3>
                    <div class="spice-row" aria-label="Spiciness level">
                        @for ($i = 0; $i < 5; $i++)
                            <i class="bi bi-fire {{ $i < $spiceLevel ? 'active' : '' }}"></i>
                        @endfor
                        <span>{{ $spiceLevel }}/5</span>
                    </div>
                </div>

                <div class="purchase-row">
                    <div class="quantity-box" aria-label="Choose quantity">
                        <button type="button" data-qty-action="decrease" aria-label="Decrease quantity">-</button>
                        <span id="productQty">1</span>
                        <button type="button" data-qty-action="increase" aria-label="Increase quantity">+</button>
                    </div>

                    <button type="button" class="btn-add-cart" id="addToCartBtn">
                        <i class="bi bi-cart-plus"></i>
                        Add to Cart
                    </button>
                </div>

                <div class="secondary-actions">
                    <button type="button" class="compare-button" id="compareButton">Compare with another item</button>
                </div>
            </section>
        </div>

        <section class="feature-panel reveal">
            <div class="feature-header">
                <span class="feature-icon"><i class="bi bi-stars"></i></span>
                <div>
                    <h2>Why You’ll Love It</h2>
                    <p>Based on your preferences and past orders</p>
                </div>
            </div>

            <div class="feature-grid">
                <div class="feature-item">
                    <span class="feature-badge badge-green"><i class="bi bi-heart"></i></span>
                    <strong>You like Pizza</strong>
                </div>
                <div class="feature-item">
                    <span class="feature-badge badge-green"><i class="bi bi-leaf"></i></span>
                    <strong>You prefer Vegetarian</strong>
                </div>
                <div class="feature-item">
                    <span class="feature-badge badge-green"><i class="bi bi-bar-chart"></i></span>
                    <strong>High in protein</strong>
                </div>
                <div class="feature-item">
                    <span class="feature-badge badge-green"><i class="bi bi-star"></i></span>
                    <strong>Popular choice</strong>
                </div>
            </div>
        </section>

        <section class="compare-panel reveal">
            <div class="compare-panel-header">
                <h2><i class="bi bi-compare"></i> Compare with Another Item</h2>
                <div class="compare-select-wrap">
                    <label for="compareSelect" class="visually-hidden">Compare with another item</label>
                    <select id="compareSelect">
                        <option value="">Choose another item</option>
                    </select>
                </div>
            </div>

            <div class="compare-content" id="comparisonTable">
                <div class="compare-empty">
                    <p>Select another menu item to compare flavor, calories, and ingredients.</p>
                </div>
            </div>
        </section>

        <section class="similar-panel reveal">
            <div class="similar-header">
                <h2>Similar Items</h2>
                <a href="{{ route('menu') }}">View All <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="similar-grid" id="similarItems"></div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const currentType = @json($type);
            const currentId = @json($item->id);
            const quantityEl = document.getElementById('productQty');
            const mainImage = document.getElementById('mainProductImage');
            const favoriteBtn = document.getElementById('productFavoriteBtn');
            const addToCartBtn = document.getElementById('addToCartBtn');
            const compareSelect = document.getElementById('compareSelect');
            const comparisonTable = document.getElementById('comparisonTable');
            const similarItems = document.getElementById('similarItems');
            const currentPrice = @json($currentPrice);
            const currentCalories = @json($currentCalories);
            const store = window.SavoraMockStore;

            let quantity = 1;

            document.querySelectorAll('[data-qty-action]').forEach((button) => {
                button.addEventListener('click', () => {
                    const action = button.dataset.qtyAction;
                    quantity = action === 'increase' ? quantity + 1 : Math.max(1, quantity - 1);
                    quantityEl.textContent = quantity;
                });
            });

            document.querySelectorAll('.gallery-thumb').forEach((thumb) => {
                thumb.addEventListener('click', () => {
                    document.querySelectorAll('.gallery-thumb').forEach((item) => item.classList.remove('active'));
                    thumb.classList.add('active');
                    mainImage.src = thumb.dataset.image;
                });
            });

            const updateFavoriteState = (isActive) => {
                if (!favoriteBtn) return;
                favoriteBtn.classList.toggle('active', isActive);
                const icon = favoriteBtn.querySelector('i');
                icon.classList.toggle('bi-heart-fill', isActive);
                icon.classList.toggle('bi-heart', !isActive);
            };

            const loadFavoriteState = () => {
                if (!isLoggedIn() || !store || !favoriteBtn) return;
                updateFavoriteState(store.isFavorite(currentType, currentId));
            };

            window.addEventListener('savora:favorites-updated', loadFavoriteState);

            favoriteBtn?.addEventListener('click', () => {
                if (!isLoggedIn()) {
                    requireLogin();
                    return;
                }

                const isActive = favoriteBtn.classList.contains('active');
                store.toggleFavorite(currentType, currentId);
                updateFavoriteState(!isActive);
                showToast(isActive ? 'Removed from favorites.' : 'Added to favorites.');
            });

            addToCartBtn?.addEventListener('click', () => {
                if (!isLoggedIn()) {
                    requireLogin();
                    return;
                }

                const product = store.findProduct(currentType, currentId);
                if (!product) {
                    showToast('Product unavailable.');
                    return;
                }

                store.addToCart(product, quantity);
                refreshHeader();
                showToast('Item added to cart');
            });

            const buildCompareRow = (label, value) => `
                <div class="compare-row">
                    <span>${label}</span>
                    <strong>${value}</strong>
                </div>
            `;

            const renderComparison = (item) => {
                if (!item) {
                    comparisonTable.innerHTML = `
                        <div class="compare-empty">
                            <p>Select another menu item to compare flavor, calories, and ingredients.</p>
                        </div>
                    `;
                    return;
                }

                const ingredients = (item.ingredients || []).join(', ') || 'Fresh ingredients';
                const spice = item.spicy_level ?? 0;

                comparisonTable.innerHTML = `
                    <div class="compare-card compare-current">
                        <div class="compare-identity">
                            <img src="${item.image_url || item.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80'}" alt="${item.name}">
                            <div>
                                <small>Current</small>
                                <h4>{{ $item->name }}</h4>
                            </div>
                        </div>
                        ${buildCompareRow('Price', `${currentPrice} EGP`)}
                        ${buildCompareRow('Calories', `${currentCalories} kcal`)}
                        ${buildCompareRow('Spice', `${spice}/5`)}
                        ${buildCompareRow('Main ingredients', ingredients)}
                    </div>
                    <div class="compare-card compare-other">
                        <div class="compare-identity">
                            <img src="${item.image_url || item.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80'}" alt="${item.name}">
                            <div>
                                <small>Selected</small>
                                <h4>${item.name}</h4>
                            </div>
                        </div>
                        ${buildCompareRow('Price', `${item.price} EGP`)}
                        ${buildCompareRow('Calories', `${item.calories || 0} kcal`)}
                        ${buildCompareRow('Spice', `${item.spicy_level || 0}/5`)}
                        ${buildCompareRow('Main ingredients', ingredients)}
                    </div>
                `;
            };

            const loadCompareOptions = () => {
                if (!store) return;

                const items = store.getProducts().filter((item) => !(item.type === currentType && item.id === currentId));
                compareSelect.innerHTML = '<option value="">Choose another item</option>';
                items.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = `${item.type}_${item.id}`;
                    option.textContent = `${item.name} (${item.type === 'food' ? 'Food' : 'Drink'})`;
                    compareSelect.appendChild(option);
                });

                compareSelect.addEventListener('change', (event) => {
                    const value = event.target.value;
                    if (!value) {
                        renderComparison(null);
                        return;
                    }

                    const [type, id] = value.split('_');
                    const item = store.findProduct(type, id);
                    renderComparison(item);
                });
            };

            const renderSimilarItems = () => {
                if (!store) return;

                const items = store.getProducts()
                    .filter((item) => !(item.type === currentType && item.id === currentId))
                    .slice(0, 4);

                similarItems.innerHTML = items.map((item) => `
                    <a href="/menu/${item.type}/${item.id}" class="similar-card">
                        <div class="similar-image">
                            <img src="${item.image_url || item.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80'}" alt="${item.name}">
                            <button type="button" class="similar-heart"><i class="bi bi-heart"></i></button>
                        </div>
                        <div class="similar-body">
                            <h3>${item.name}</h3>
                            <div class="similar-meta">
                                <span>${item.price} EGP</span>
                                <span><i class="bi bi-stars"></i> 96%</span>
                            </div>
                        </div>
                    </a>
                `).join('');
            };

            document.getElementById('compareButton')?.addEventListener('click', () => {
                document.getElementById('compareSelect')?.focus();
            });

            loadFavoriteState();
            loadCompareOptions();
            renderSimilarItems();
        });
    </script>
@endpush
