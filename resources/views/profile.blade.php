@extends('layouts.app')

@section('title', 'Profile - Savora Cafeteria')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/account.css') }}">
@endpush

@section('content')
    @php
        $user = auth()->user();
        $userName = trim((string) ($user->name ?? 'Guest Customer')) ?: 'Guest Customer';
        $userEmail = $user->email ?? 'No email provided';
        $userPhone = $user->phone ?? 'No phone provided';
        $userAge = $user->age ?? 'Not set';
        $memberSince = $user?->created_at ? $user->created_at->translatedFormat('M Y') : 'Just joined';
        $ordersCount = $user ? $user->orders()->count() : 0;
        $favoriteCount = $user ? $user->favorites()->count() : 0;
        $preference = $user?->preference;
        $profileImage = data_get($user, 'avatar') ?? data_get($user, 'profile_photo_url') ?? null;
        $initials = collect(explode(' ', $userName))->take(2)->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))->implode('') ?: 'G';
        $favoriteCategories = $preference?->favorite_categories ?? ['Pizza', 'Pasta'];
        $favoriteFoodTypes = $preference?->favorite_food_types ?? ['Italian', 'Healthy'];
        $favoriteBeverages = $preference?->favorite_beverages ?? ['Coffee', 'Juices'];
        $preferredTaste = $preference?->preferred_taste ?? ['Savory', 'Sweet'];
        $dietaryPreferences = $preference?->dietary_preferences ?? ['Vegetarian'];
        $pricePreference = $preference?->price_preference ?? '150 - 250';
        $favoriteIngredients = $preference?->favorite_ingredients ?? ['Cheese', 'Tomato'];
        $dislikedIngredients = $preference?->disliked_ingredients ?? ['Olives', 'Garlic'];
        $spicyLevel = $preference?->spicy_level ?? 'Medium';
    @endphp

    <div class="account-shell page-shell">
        <aside class="account-sidebar reveal">
            <nav class="side-nav">
                <a href="{{ route('profile') }}" class="side-link active">
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
                <a href="#" class="side-link">
                    <i class="bi bi-robot"></i>
                    <span>AI Chatbot</span>
                </a>
            </nav>

            <div class="promo-box">
                <p>Good food brings people together</p>
            </div>
        </aside>

        <main class="account-main reveal">
            <section class="profile-hero">
                <div class="profile-avatar-wrap">
                    @if ($profileImage)
                        <img src="{{ $profileImage }}" alt="{{ $userName }} profile picture">
                    @else
                        <div class="profile-avatar-empty" aria-label="{{ $userName }} avatar placeholder"><span>{{ $initials }}</span></div>
                    @endif
                </div>

                <div class="profile-meta">
                    <div>
                        <h1 id="profileName">{{ $userName }}</h1>
                        <div class="meta-inline">
                            <span id="profileRole" class="dot-badge">{{ ucfirst($user->role ?? 'customer') }}</span>
                            <span id="profileEmail" class="meta-email">{{ $userEmail }}</span>
                        </div>
                    </div>

                    <div class="profile-info-list">
                        <span id="profilePhone"><i class="bi bi-telephone"></i> {{ $userPhone }}</span>
                        <span id="profileAge"><i class="bi bi-calendar3"></i> {{ $userAge }}</span>
                    </div>
                </div>

                <button type="button" class="edit-btn">Edit Profile</button>
            </section>

            <section class="stats-grid">
                <article class="stat-card">
                    <div class="stat-icon"><i class="bi bi-bag-check"></i></div>
                    <div class="stat-value">{{ $ordersCount }}</div>
                    <div class="stat-label">Total Orders</div>
                </article>

                <article class="stat-card">
                    <div class="stat-icon"><i class="bi bi-heart"></i></div>
                    <div class="stat-value">{{ $favoriteCount }}</div>
                    <div class="stat-label">Favorite Items</div>
                </article>

                <article class="stat-card">
                    <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
                    <div class="stat-value">{{ $memberSince }}</div>
                    <div class="stat-label">Member Since</div>
                </article>

                <article class="stat-card">
                    <div class="stat-icon"><i class="bi bi-fire"></i></div>
                    <div class="stat-value">{{ $spicyLevel }}</div>
                    <div class="stat-label">Spicy Level</div>
                </article>
            </section>

            <section class="panel preferences-panel" style="margin-bottom: 1.5rem;">
                <div class="panel-head">
                    <div class="panel-title-wrap">
                        <i class="bi bi-sliders"></i>
                        <h2>My Preferences</h2>
                    </div>
                    <a href="{{ route('preferences') }}" class="btn btn-savora btn-sm">Manage Preferences</a>
                </div>
                <div class="tag-row" id="profilePreferencesSummary">
                    <span class="tag tag-soft">Loading preferences...</span>
                </div>
            </section>

            <section class="dashboard-grid">
                <div class="panel profile-panel">
                    <div class="panel-head">
                        <div class="panel-title-wrap">
                            <i class="bi bi-person-vcard"></i>
                            <h2>Personal Information</h2>
                        </div>
                        <button type="button" class="mini-edit">Edit</button>
                    </div>

                    <div class="info-row">
                        <span class="label">Full Name</span>
                        <span id="profileInfoName" class="value">{{ $userName }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Email</span>
                        <span id="profileInfoEmail" class="value">{{ $userEmail }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Phone</span>
                        <span id="profileInfoPhone" class="value">{{ $userPhone }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Age</span>
                        <span id="profileInfoAge" class="value">{{ $userAge }}</span>
                    </div>
                </div>

                <div class="panel profile-panel">
                    <div class="panel-head">
                        <div class="panel-title-wrap">
                            <i class="bi bi-pie-chart-fill"></i>
                            <h2>Profile Stats</h2>
                        </div>
                    </div>

                    <div class="progress-box">
                        <div class="progress-ring">
                            <div class="progress-ring-inner">
                                <strong>{{ min(98, max(35, ($favoriteCount + $ordersCount) * 8)) }}%</strong>
                                <span>Profile</span>
                                <small>Completed</small>
                            </div>
                        </div>
                    </div>

                    <ul class="check-list">
                        <li><i class="bi bi-check2"></i> Basic Information</li>
                        <li><i class="bi bi-check2"></i> Food Preferences</li>
                        <li><i class="bi bi-check2"></i> Dietary Preferences</li>
                        <li><i class="bi bi-check2"></i> Favorite Ingredients</li>
                    </ul>
                </div>
            </section>

            <section class="preferences-panel panel">
                <div class="panel-head">
                    <div class="panel-title-wrap">
                        <i class="bi bi-heart-fill"></i>
                        <h2>Food Preferences</h2>
                    </div>
                    <button type="button" class="mini-edit">Edit</button>
                </div>

                <div class="tags-block" data-preference-group="favorite_categories">
                    <h3>Favorite Categories</h3>
                    <div class="tag-row" id="favoriteCategoriesList">
                        @foreach ($favoriteCategories as $category)
                            <span class="tag tag-green">{{ $category }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="tags-block" data-preference-group="favorite_food_types">
                    <h3>Favorite Food Types</h3>
                    <div class="tag-row" id="favoriteFoodTypesList">
                        @foreach ($favoriteFoodTypes as $type)
                            <span class="tag tag-amber">{{ $type }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="tags-block" data-preference-group="favorite_beverages">
                    <h3>Favorite Beverages</h3>
                    <div class="tag-row" id="favoriteBeveragesList">
                        @foreach ($favoriteBeverages as $drink)
                            <span class="tag tag-soft">{{ $drink }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="tags-block" data-preference-group="preferred_taste">
                    <h3>Preferred Taste</h3>
                    <div class="tag-row" id="preferredTasteList">
                        @foreach ($preferredTaste as $taste)
                            <span class="tag tag-red">{{ $taste }}</span>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="lower-grid">
                <section class="panel preferences-list">
                    <div class="panel-head">
                        <div class="panel-title-wrap">
                            <i class="bi bi-emoji-smile"></i>
                            <h2>Dietary Preferences</h2>
                        </div>
                        <button type="button" class="mini-edit">Edit</button>
                    </div>

                    <div class="tag-row" id="dietaryPreferencesList">
                        @foreach ($dietaryPreferences as $diet)
                            <span class="tag tag-green">{{ $diet }}</span>
                        @endforeach
                    </div>
                </section>

                <section class="panel preferences-list">
                    <div class="panel-head">
                        <div class="panel-title-wrap">
                            <i class="bi bi-wallet2"></i>
                            <h2>Price Preference</h2>
                        </div>
                        <button type="button" class="mini-edit">Edit</button>
                    </div>

                    <div class="price-pref" id="pricePreferenceBox">
                        <div class="price-range">
                            <span>0 EGP</span>
                            <span>300 EGP</span>
                        </div>
                        <div class="range-track">
                            <span class="range-fill"></span>
                        </div>
                        <div class="pref-label">Most preferred</div>
                        <strong>{{ $pricePreference }} EGP</strong>
                    </div>
                </section>
            </div>

            <section class="panel tag-panel">
                <div class="panel-head">
                    <div class="panel-title-wrap">
                        <i class="bi bi-egg-fried"></i>
                        <h2>Favorite Ingredients</h2>
                    </div>
                    <button type="button" class="mini-edit">Edit</button>
                </div>

                <div class="tag-row" id="favoriteIngredientsList">
                    @foreach ($favoriteIngredients as $ingredient)
                        <span class="tag tag-soft">{{ $ingredient }}</span>
                    @endforeach
                </div>
            </section>

            <section class="panel tag-panel">
                <div class="panel-head">
                    <div class="panel-title-wrap">
                        <i class="bi bi-slash-circle"></i>
                        <h2>Disliked Ingredients</h2>
                    </div>
                    <button type="button" class="mini-edit">Edit</button>
                </div>

                <div class="tag-row" id="dislikedIngredientsList">
                    @foreach ($dislikedIngredients as $ingredient)
                        <span class="tag tag-muted">{{ $ingredient }}</span>
                    @endforeach
                </div>
            </section>
        </main>

        <aside class="account-sidecards reveal">
            <div class="right-card recent-orders">
                <div class="panel-head small-head">
                    <div class="panel-title-wrap">
                        <i class="bi bi-clock-history"></i>
                        <h2>Recent Orders</h2>
                    </div>
                    <a href="{{ route('orders') }}" class="mini-link">View All</a>
                </div>

                <ul class="recent-list">
                    @php
                        $recentOrders = $user ? $user->orders()->latest()->take(4)->get() : collect();
                    @endphp

                    @forelse ($recentOrders as $recentOrder)
                        @php
                            $firstItem = $recentOrder->items()->first();
                            $orderable = $firstItem?->orderable;
                            $image = $orderable?->image ? asset('storage/' . ltrim($orderable->image, '/')) : 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=400&q=80';
                            $itemName = $orderable?->name ?? 'Order item';
                        @endphp
                        <li>
                            <img src="{{ $image }}" alt="{{ $itemName }}">
                            <div>
                                <strong>{{ $itemName }}</strong>
                                <small>{{ $recentOrder->total_price }} EGP</small>
                                <span>{{ ucfirst($recentOrder->status ?? 'pending') }}</span>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </li>
                    @empty
                        <li>
                            <div>
                                <strong>No recent orders yet</strong>
                                <small>Start exploring the menu.</small>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="right-card help-card">
                <div class="help-bot">
                    <i class="bi bi-robot"></i>
                </div>
                <h3>Need Help?</h3>
                <p>Chat with our AI to get personalized food recommendations, answer your questions, and more!</p>
                <button type="button" class="chat-btn">Chat with AI <i class="bi bi-arrow-right"></i></button>
            </div>
        </aside>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title"><i class="bi bi-person-vcard me-2"></i>Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm">
                        <div class="mb-3">
                            <label for="editProfileName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editProfileName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProfileEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editProfileEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProfilePhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editProfilePhone" name="phone" placeholder="01012345678">
                        </div>
                        <div class="mb-3">
                            <label for="editProfileAge" class="form-label">Age</label>
                            <input type="number" class="form-control" id="editProfileAge" name="age" min="10" max="100">
                        </div>
                        <div id="profileUpdateErrors" class="mb-3"></div>
                        <button type="submit" class="btn btn-savora w-100">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const modalEl = document.getElementById('editProfileModal');
            const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
            const form = document.getElementById('editProfileForm');
            const errorBox = document.getElementById('profileUpdateErrors');

            const setText = (selector, value) => {
                const node = document.querySelector(selector);
                if (node) node.textContent = value;
            };

            const setHtml = (selector, value) => {
                const node = document.querySelector(selector);
                if (node) node.innerHTML = value;
            };

            const renderTagList = (selector, values = [], cssClass = 'tag tag-soft') => {
                const container = document.querySelector(selector);
                if (!container) return;

                const safeValues = Array.isArray(values) && values.length ? values : ['Not set'];
                container.innerHTML = safeValues.map((value) => `<span class="${cssClass}">${String(value)}</span>`).join('');
            };

            const renderProfilePreferences = (preferencePayload = {}) => {
                const container = document.getElementById('profilePreferencesSummary');
                if (container) {
                    const values = [
                        ...(Array.isArray(preferencePayload.favorite_categories) ? preferencePayload.favorite_categories : []),
                        ...(Array.isArray(preferencePayload.favorite_food_types) ? preferencePayload.favorite_food_types : []),
                        ...(Array.isArray(preferencePayload.favorite_beverages) ? preferencePayload.favorite_beverages : []),
                    ];

                    const finalValues = values.length ? values.slice(0, 6).map((value) => value.toString()) : ['No preferences saved yet'];
                    container.innerHTML = finalValues.map((value) => `<span class="tag tag-soft">${value}</span>`).join('');
                }

                renderTagList('#favoriteCategoriesList', preferencePayload.favorite_categories || [], 'tag tag-green');
                renderTagList('#favoriteFoodTypesList', preferencePayload.favorite_food_types || [], 'tag tag-amber');
                renderTagList('#favoriteBeveragesList', preferencePayload.favorite_beverages || [], 'tag tag-soft');
                renderTagList('#preferredTasteList', preferencePayload.preferred_taste ? [preferencePayload.preferred_taste] : [], 'tag tag-red');
                renderTagList('#dietaryPreferencesList', preferencePayload.dietary_preferences || [], 'tag tag-green');
                renderTagList('#favoriteIngredientsList', preferencePayload.favorite_ingredients || [], 'tag tag-soft');
                renderTagList('#dislikedIngredientsList', preferencePayload.disliked_ingredients || [], 'tag tag-muted');

                const priceBox = document.getElementById('pricePreferenceBox');
                if (priceBox) {
                    const price = preferencePayload.price_preference ? `${preferencePayload.price_preference}` : 'Not set';
                    priceBox.innerHTML = `
                        <div class="price-range">
                            <span>0 EGP</span>
                            <span>300 EGP</span>
                        </div>
                        <div class="range-track">
                            <span class="range-fill"></span>
                        </div>
                        <div class="pref-label">Most preferred</div>
                        <strong>${price} EGP</strong>
                    `;
                }
            };

            const hydrateProfile = async () => {
                const savedUser = window.SavoraMockStore?.getCurrentUser?.() || JSON.parse(localStorage.getItem('savora_user') || 'null');
                let user = savedUser;

                try {
                    const response = await fetch('/api/profile', { headers: authHeaders() });
                    if (response.ok) {
                        const data = await response.json();
                        user = data.user || user;
                        if (user && user.email) {
                            localStorage.setItem('savora_user', JSON.stringify(user));
                        }
                    }
                } catch (error) {
                    // Fallback to the existing mock user data.
                }

                if (!user) {
                    return;
                }

                const fullName = user.name || 'Guest Customer';
                const initials = fullName
                    .split(' ')
                    .filter(Boolean)
                    .slice(0, 2)
                    .map((part) => part.charAt(0).toUpperCase())
                    .join('') || 'G';

                setText('#profileName', fullName);
                setText('#profileRole', (user.role || 'customer').charAt(0).toUpperCase() + (user.role || 'customer').slice(1));
                setText('#profileEmail', user.email || 'No email provided');
                setHtml('#profilePhone', '<i class="bi bi-telephone"></i> ' + (user.phone || 'No phone provided'));
                setHtml('#profileAge', '<i class="bi bi-calendar3"></i> ' + (user.age || 'Not set'));
                setText('#profileInfoName', fullName);
                setText('#profileInfoEmail', user.email || 'No email provided');
                setText('#profileInfoPhone', user.phone || 'No phone provided');
                setText('#profileInfoAge', user.age || 'Not set');

                const avatar = document.querySelector('.profile-avatar-empty');
                if (avatar) {
                    avatar.innerHTML = '<span>' + initials + '</span>';
                }

                if (form) {
                    form.querySelector('#editProfileName').value = user.name || '';
                    form.querySelector('#editProfileEmail').value = user.email || '';
                    form.querySelector('#editProfilePhone').value = user.phone || '';
                    form.querySelector('#editProfileAge').value = user.age || '';
                }
            };

            const hydratePreferences = async () => {
                const cachedPreference = JSON.parse(localStorage.getItem('savora_preferences_snapshot') || 'null');
                if (cachedPreference) {
                    renderProfilePreferences(cachedPreference);
                }

                try {
                    const response = await fetch('/api/profile/preferences', { headers: authHeaders() });
                    if (!response.ok) {
                        renderProfilePreferences(cachedPreference || {});
                        return;
                    }

                    const data = await response.json();
                    const preference = data.preference || cachedPreference || {};
                    localStorage.setItem('savora_preferences_snapshot', JSON.stringify(preference));
                    renderProfilePreferences(preference);
                } catch (error) {
                    renderProfilePreferences(cachedPreference || {});
                }
            };

            window.addEventListener('savora:preferences-updated', (event) => {
                const preference = event?.detail || JSON.parse(localStorage.getItem('savora_preferences_snapshot') || 'null');
                if (preference) {
                    renderProfilePreferences(preference);
                    localStorage.setItem('savora_preferences_snapshot', JSON.stringify(preference));
                }
            });

            document.querySelectorAll('.edit-btn, .mini-edit').forEach((button) => {
                button.addEventListener('click', () => {
                    modal?.show();
                });
            });

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (!errorBox) return;

                errorBox.innerHTML = '';
                const formData = new FormData(form);
                const payload = {
                    name: String(formData.get('name') || '').trim(),
                    email: String(formData.get('email') || '').trim(),
                    phone: String(formData.get('phone') || '').trim() || null,
                    age: formData.get('age') ? Number(formData.get('age')) : null,
                };

                if (!payload.name || !payload.email) {
                    errorBox.innerHTML = '<div class="error-item"><i class="bi bi-exclamation-circle-fill"></i><span>Name and email are required.</span></div>';
                    return;
                }

                try {
                    const response = await fetch('/api/profile', {
                        method: 'PUT',
                        headers: authHeaders(),
                        body: JSON.stringify(payload),
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : (data.message || 'Unable to update profile.');
                        errorBox.innerHTML = `<div class="error-item"><i class="bi bi-exclamation-circle-fill"></i><span>${firstError}</span></div>`;
                        return;
                    }

                    if (data.user && data.user.email) {
                        localStorage.setItem('savora_user', JSON.stringify(data.user));
                    }

                    showToast('Profile updated successfully.');
                    modal?.hide();
                    await hydrateProfile();
                    refreshHeader();
                } catch (error) {
                    errorBox.innerHTML = '<div class="error-item"><i class="bi bi-exclamation-circle-fill"></i><span>Unable to save profile changes.</span></div>';
                }
            });

            await hydrateProfile();
            await hydratePreferences();
        });
    </script>
@endpush
