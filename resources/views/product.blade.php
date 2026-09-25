@php
    $ingredients = collect($item->ingredients ?? [])->map(fn ($value) => trim((string) $value))->filter()->values()->all();
    $spiceLevel = max(0, min(5, (int) ($item->spicy_level ?? 0)));
    $categoryName = $item->category?->name ?? (($type === 'food') ? 'Food item' : 'Drink');
    $itemImage = $item->image ? asset('storage/' . $item->image) : null;
    $placeholderIcon = $type === 'food' ? 'bi-egg-fried' : 'bi-cup-straw';
    $imageUrl = $itemImage ?? null;
    $showTempCard = $type === 'beverage' && ! empty($item->temperature);
    $showPrepCard = $type === 'food';
    $showSizeCard = $type === 'beverage' && ! empty($item->size);
    $hasSpiciness = $type === 'food';

    $heatLabel = $type === 'food'
        ? ($spiceLevel >= 3 ? 'Hot' : ($spiceLevel >= 1 ? 'Mild' : 'Smooth'))
        : ucfirst((string) ($item->temperature ?? 'Hot'));

    $currentItemPayload = [
        'id' => $item->id,
        'type' => $type,
        'name' => $item->name,
        'price' => (float) $item->price,
        'calories' => (int) ($item->calories ?? 0),
        'spicy_level' => (int) ($item->spicy_level ?? 0),
        'temperature' => $item->temperature ?? null,
        'ingredients' => $ingredients,
        'image_url' => $imageUrl,
    ];
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
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $item->name }}" id="mainProductImage">
                    @else
                        <div class="placeholder-hero" aria-label="No image available">
                            <i class="bi {{ $placeholderIcon }}"></i>
                        </div>
                    @endif
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
                    <span class="match-pill d-none" id="matchPill"><i class="bi bi-stars"></i> 0% match</span>
                </div>

                <div class="price-row">
                    <div class="product-price">{{ number_format((float) $item->price, 2) }} EGP</div>
                    <div class="product-tags">
                        <span class="tag tag-terracotta"><i class="bi bi-fire"></i> {{ $heatLabel }}</span>
                    </div>
                </div>

                <p class="product-description">
                    {{ $item->description ?: 'A signature Savora favorite made with fresh ingredients, balanced seasoning, and rich flavor.' }}
                </p>

                <div class="info-grid">
                    <div class="info-card">
                        <span class="info-icon"><i class="bi bi-fire"></i></span>
                        <div>
                            <small>Calories</small>
                            <strong>{{ (int) ($item->calories ?? 0) }} kcal</strong>
                        </div>
                    </div>

                    @if ($showTempCard)
                        <div class="info-card">
                            <span class="info-icon"><i class="bi bi-thermometer-half"></i></span>
                            <div>
                                <small>Temp</small>
                                <strong>{{ ucfirst((string) $item->temperature) }}</strong>
                            </div>
                        </div>
                    @endif

                    @if ($showPrepCard)
                        <div class="info-card">
                            <span class="info-icon"><i class="bi bi-clock"></i></span>
                            <div>
                                <small>Prep Time</small>
                                <strong>{{ (int) ($item->preparation_time ?? 15) }} min</strong>
                            </div>
                        </div>
                    @endif

                    @if ($showSizeCard)
                        <div class="info-card">
                            <span class="info-icon"><i class="bi bi-rulers"></i></span>
                            <div>
                                <small>Size</small>
                                <strong>{{ ucfirst((string) $item->size) }}</strong>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="detail-section">
                    <h3>Ingredients</h3>
                    <div class="chip-list">
                        @forelse ($ingredients as $ingredient)
                            <span class="chip">{{ $ingredient }}</span>
                        @empty
                            <span class="chip">Fresh ingredients</span>
                            <span class="chip">Savora recipe</span>
                            <span class="chip">Premium herbs</span>
                        @endforelse
                    </div>
                </div>

                @if ($hasSpiciness)
                    <div class="detail-section">
                        <h3>Spiciness level</h3>
                        <div class="spice-row" aria-label="Spiciness level">
                            @for ($i = 0; $i < 5; $i++)
                                <i class="bi bi-fire {{ $i < $spiceLevel ? 'active' : '' }}"></i>
                            @endfor
                            <span>{{ $spiceLevel }}/5</span>
                        </div>
                    </div>
                @endif

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

        <section class="feature-panel reveal" id="featurePanel">
            <div class="feature-header">
                <span class="feature-icon"><i class="bi bi-stars"></i></span>
                <div>
                    <h2>Why You’ll Love It</h2>
                    <p>Based on your preferences and past orders</p>
                </div>
            </div>

            <div class="feature-grid" id="featureGrid">
                <p class="text-muted small mb-0">Loading your recommendations...</p>
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
        document.addEventListener('DOMContentLoaded', async function () {
            const currentType = @json($type);
            const currentId = @json($item->id);
            const currentItemData = @json($currentItemPayload);
            const quantityEl = document.getElementById('productQty');
            const favoriteBtn = document.getElementById('productFavoriteBtn');
            const addToCartBtn = document.getElementById('addToCartBtn');
            const compareSelect = document.getElementById('compareSelect');
            const comparisonTable = document.getElementById('comparisonTable');
            const featurePanel = document.getElementById('featurePanel');
            const featureGrid = document.getElementById('featureGrid');
            const matchPill = document.getElementById('matchPill');
            const similarItems = document.getElementById('similarItems');
            let quantity = 1;

            document.querySelectorAll('[data-qty-action]').forEach((button) => {
                button.addEventListener('click', () => {
                    const action = button.dataset.qtyAction;
                    quantity = action === 'increase' ? quantity + 1 : Math.max(1, quantity - 1);
                    quantityEl.textContent = quantity;
                });
            });

            const updateFavoriteState = (isActive) => {
                if (!favoriteBtn) return;
                favoriteBtn.classList.toggle('active', isActive);
                const icon = favoriteBtn.querySelector('i');
                icon.classList.toggle('bi-heart-fill', isActive);
                icon.classList.toggle('bi-heart', !isActive);
            };

            const loadFavoriteState = async () => {
                if (!isLoggedIn() || !favoriteBtn) return;
                const response = await fetch('/api/favorites', { headers: authHeaders(false) });
                const payload = await response.json();
                updateFavoriteState((payload.data || []).some((favorite) => favorite.type === currentType && Number(favorite.item?.id) === Number(currentId)));
            };

            window.addEventListener('savora:favorites-updated', loadFavoriteState);

            favoriteBtn?.addEventListener('click', async () => {
                if (!isLoggedIn()) {
                    requireLogin();
                    return;
                }

                const isActive = favoriteBtn.classList.contains('active');
                const response = await fetch(isActive ? `/api/favorites/${currentType}/${currentId}` : '/api/favorites', {
                    method: isActive ? 'DELETE' : 'POST',
                    headers: authHeaders(!isActive),
                    body: isActive ? undefined : JSON.stringify({ type: currentType, id: currentId }),
                });
                if (!response.ok) {
                    showToast('Unable to update favorites.');
                    return;
                }
                updateFavoriteState(!isActive);
                window.dispatchEvent(new CustomEvent('savora:favorites-updated'));
                showToast(isActive ? 'Removed from favorites.' : 'Added to favorites.');
            });

            addToCartBtn?.addEventListener('click', async () => {
                if (!isLoggedIn()) {
                    requireLogin();
                    return;
                }

                const response = await fetch('/api/cart/items', {
                    method: 'POST', headers: authHeaders(),
                    body: JSON.stringify({ type: currentType, id: currentId, quantity }),
                });
                if (!response.ok) {
                    showToast('Product unavailable.');
                    return;
                }
                refreshHeader();
                showToast('Item added to cart');
            });

            function loadWhyYouLoveIt() {
                if (!isLoggedIn()) {
                    matchPill?.remove();
                    featurePanel?.remove();
                    return;
                }

                const product = currentItemData;
                if (!product || !featureGrid) {
                    matchPill?.remove();
                    featurePanel?.remove();
                    return;
                }

                const reasons = [
                    `You selected ${product.name}, which matches your preferred ${product.type} choice.`,
                    `${product.ingredients?.[0] || 'Fresh ingredients'} is one of your favorite ingredients.`,
                    `This item fits your ${product.price <= 150 ? 'budget-friendly' : 'premium'} meal preference.`,
                ];

                if (matchPill) {
                    matchPill.classList.remove('d-none');
                    matchPill.innerHTML = '<i class="bi bi-stars"></i> 94% match';
                }

                featureGrid.innerHTML = reasons.map((reason) => `
                    <div class="feature-item">
                        <span class="feature-badge badge-green"><i class="bi bi-check2"></i></span>
                        <strong>${reason}</strong>
                    </div>
                `).join('');
            }

            function loadCompareOptions() {
                if (!compareSelect) return;

                const [foodResponse, beverageResponse] = await Promise.all([fetch('/api/food-items?per_page=50'), fetch('/api/beverages?per_page=50')]);
                const [foodPayload, beveragePayload] = await Promise.all([foodResponse.json(), beverageResponse.json()]);
                const items = [...(foodPayload.data || []), ...(beveragePayload.data || [])].filter((item) => !(item.type === currentType && item.id === Number(currentId)));
                compareSelect.innerHTML = '<option value="">Choose another item</option>';
                items.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = `${item.type}_${item.id}`;
                    option.textContent = item.name;
                    compareSelect.appendChild(option);
                });

                compareSelect.addEventListener('change', (event) => {
                    const value = event.target.value;
                    if (!value) {
                        comparisonTable.innerHTML = `
                            <div class="compare-empty">
                                <p>Select another menu item to compare flavor, calories, and ingredients.</p>
                            </div>
                        `;
                        return;
                    }

                    const [otherType, otherId] = value.split('_');
                    const current = currentItemData;
                    const other = items.find((item) => item.type === otherType && Number(item.id) === Number(otherId));

                    comparisonTable.innerHTML = `
                        <div class="compare-card compare-current">
                            <div class="compare-identity">
                                <img src="${current.image_url || current.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80'}" alt="${current.name}">
                                <div>
                                    <small>Current</small>
                                    <h4>${current.name}</h4>
                                </div>
                            </div>
                            <div class="compare-row"><span>Price</span><strong>${current.price} EGP</strong></div>
                            <div class="compare-row"><span>Calories</span><strong>${current.calories || 0} kcal</strong></div>
                            <div class="compare-row"><span>Spice</span><strong>${current.spicy_level || 0}/5</strong></div>
                            <div class="compare-row"><span>Main ingredients</span><strong>${(current.ingredients || []).join(', ') || 'Fresh ingredients'}</strong></div>
                        </div>
                        <div class="compare-card compare-other">
                            <div class="compare-identity">
                                <img src="${other.image_url || other.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80'}" alt="${other.name}">
                                <div>
                                    <small>Selected</small>
                                    <h4>${other.name}</h4>
                                </div>
                            </div>
                            <div class="compare-row"><span>Price</span><strong>${other.price} EGP</strong></div>
                            <div class="compare-row"><span>Calories</span><strong>${other.calories || 0} kcal</strong></div>
                            <div class="compare-row"><span>Spice</span><strong>${other.spicy_level || 0}/5</strong></div>
                            <div class="compare-row"><span>Main ingredients</span><strong>${(other.ingredients || []).join(', ') || 'Fresh ingredients'}</strong></div>
                        </div>
                    `;
                });
            }

            async function loadSimilarItems() {
                if (!similarItems) return;

                const [foodResponse, beverageResponse] = await Promise.all([fetch('/api/food-items?per_page=10'), fetch('/api/beverages?per_page=10')]);
                const [foodPayload, beveragePayload] = await Promise.all([foodResponse.json(), beverageResponse.json()]);
                const items = [...(foodPayload.data || []), ...(beveragePayload.data || [])].filter((item) => !(item.type === currentType && item.id === Number(currentId))).slice(0, 4);
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
                            </div>
                        </div>
                    </a>
                `).join('');
            }

            document.getElementById('compareButton')?.addEventListener('click', () => {
                compareSelect?.focus();
            });

            loadFavoriteState();
            loadWhyYouLoveIt();
            loadCompareOptions();
            loadSimilarItems();
        });
    </script>
@endpush
