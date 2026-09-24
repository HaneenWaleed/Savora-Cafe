@extends('layouts.app')

@section('title', 'Preferences - Savora Cafeteria')

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
                <a href="{{ route('favorites') }}" class="side-link">
                    <i class="bi bi-heart"></i>
                    <span>Favorites</span>
                </a>
                <a href="{{ route('preferences') }}" class="side-link active">
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

        <main class="account-main reveal preferences-main">
            <div class="subpage-topbar">
                <div>
                    <span class="mini-label">Your tastes</span>
                    <h1>Preferences</h1>
                </div>
                <a href="{{ route('profile') }}" class="btn btn-outline-dark btn-sm">Back to Profile</a>
            </div>

            <form id="preferencesForm" class="panel">
                <div class="panel-head">
                    <div class="panel-title-wrap">
                        <i class="bi bi-sliders"></i>
                        <h2>My Preferences</h2>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Favorite Categories</label>
                            <div class="tag-row checkbox-group" data-name="favorite_categories">
                                <label class="tag tag-green"><input type="checkbox" value="Pizza"> Pizza</label>
                                <label class="tag tag-green"><input type="checkbox" value="Burgers"> Burgers</label>
                                <label class="tag tag-green"><input type="checkbox" value="Pasta"> Pasta</label>
                                <label class="tag tag-green"><input type="checkbox" value="Sandwiches"> Sandwiches</label>
                                <label class="tag tag-green"><input type="checkbox" value="Coffee"> Coffee</label>
                                <label class="tag tag-green"><input type="checkbox" value="Juices"> Juices</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Favorite Food Types</label>
                            <div class="tag-row checkbox-group" data-name="favorite_food_types">
                                <label class="tag tag-amber"><input type="checkbox" value="Chicken"> Chicken</label>
                                <label class="tag tag-amber"><input type="checkbox" value="Fast Food"> Fast Food</label>
                                <label class="tag tag-amber"><input type="checkbox" value="Healthy"> Healthy</label>
                                <label class="tag tag-amber"><input type="checkbox" value="Italian"> Italian</label>
                                <label class="tag tag-amber"><input type="checkbox" value="Vegetarian"> Vegetarian</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Favorite Beverages</label>
                            <div class="tag-row checkbox-group" data-name="favorite_beverages">
                                <label class="tag tag-soft"><input type="checkbox" value="Coffee"> Coffee</label>
                                <label class="tag tag-soft"><input type="checkbox" value="Juices"> Juices</label>
                                <label class="tag tag-soft"><input type="checkbox" value="Tea"> Tea</label>
                                <label class="tag tag-soft"><input type="checkbox" value="Smoothies"> Smoothies</label>
                                <label class="tag tag-soft"><input type="checkbox" value="Soda"> Soda</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Preferred Taste</label>
                            <div class="tag-row radio-group" data-name="preferred_taste">
                                <label class="tag tag-red"><input type="radio" name="preferred_taste" value="savory"> Savory</label>
                                <label class="tag tag-red"><input type="radio" name="preferred_taste" value="sweet"> Sweet</label>
                                <label class="tag tag-red"><input type="radio" name="preferred_taste" value="spicy"> Spicy</label>
                                <label class="tag tag-red"><input type="radio" name="preferred_taste" value="sour"> Sour</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Dietary Preferences</label>
                            <div class="tag-row checkbox-group" data-name="dietary_preferences">
                                <label class="tag tag-green"><input type="checkbox" value="vegetarian"> Vegetarian</label>
                                <label class="tag tag-green"><input type="checkbox" value="vegan"> Vegan</label>
                                <label class="tag tag-green"><input type="checkbox" value="low-calorie"> Low Calorie</label>
                                <label class="tag tag-green"><input type="checkbox" value="high-protein"> High Protein</label>
                                <label class="tag tag-green"><input type="checkbox" value="low-carb"> Low Carb</label>
                                <label class="tag tag-green"><input type="checkbox" value="gluten-free"> Gluten Free</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Price Preference</label>
                            <div class="tag-row radio-group" data-name="price_preference">
                                <label class="tag tag-soft"><input type="radio" name="price_preference" value="low"> Low</label>
                                <label class="tag tag-soft"><input type="radio" name="price_preference" value="medium"> Medium</label>
                                <label class="tag tag-soft"><input type="radio" name="price_preference" value="high"> High</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Spicy Level</label>
                            <div class="tag-row radio-group" data-name="spicy_level">
                                <label class="tag tag-muted"><input type="radio" name="spicy_level" value="0"> 0</label>
                                <label class="tag tag-muted"><input type="radio" name="spicy_level" value="1"> 1</label>
                                <label class="tag tag-muted"><input type="radio" name="spicy_level" value="2"> 2</label>
                                <label class="tag tag-muted"><input type="radio" name="spicy_level" value="3"> 3</label>
                                <label class="tag tag-muted"><input type="radio" name="spicy_level" value="4"> 4</label>
                                <label class="tag tag-muted"><input type="radio" name="spicy_level" value="5"> 5</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label for="favoriteIngredients" class="form-label">Favorite Ingredients</label>
                            <input type="text" class="form-control" id="favoriteIngredients" name="favoriteIngredients" placeholder="Chicken, Cheese">
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label for="dislikedIngredients" class="form-label">Disliked Ingredients</label>
                            <input type="text" class="form-control" id="dislikedIngredients" name="dislikedIngredients" placeholder="Mushrooms, Olives">
                        </div>
                    </div>
                </div>

                <div id="preferencesErrors" class="mb-3"></div>
                <button type="submit" class="btn btn-savora">Save Preferences</button>
            </form>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const form = document.getElementById('preferencesForm');
            const errorBox = document.getElementById('preferencesErrors');

            const collectCheckedValues = (selector) => Array.from(document.querySelectorAll(`${selector}:checked`)).map((input) => input.value);

            const parseCsv = (value) => String(value || '')
                .split(',')
                .map((item) => item.trim())
                .filter(Boolean);

            const applyPreferenceData = (preference = {}) => {
                const selectedCategories = new Set(Array.isArray(preference.favorite_categories) ? preference.favorite_categories : []);
                document.querySelectorAll('[data-name="favorite_categories"] input').forEach((input) => {
                    input.checked = selectedCategories.has(input.value);
                });

                const foodTypes = new Set(Array.isArray(preference.favorite_food_types) ? preference.favorite_food_types : []);
                document.querySelectorAll('[data-name="favorite_food_types"] input').forEach((input) => {
                    input.checked = foodTypes.has(input.value);
                });

                const beverages = new Set(Array.isArray(preference.favorite_beverages) ? preference.favorite_beverages : []);
                document.querySelectorAll('[data-name="favorite_beverages"] input').forEach((input) => {
                    input.checked = beverages.has(input.value);
                });

                const dietary = new Set(Array.isArray(preference.dietary_preferences) ? preference.dietary_preferences : []);
                document.querySelectorAll('[data-name="dietary_preferences"] input').forEach((input) => {
                    input.checked = dietary.has(input.value);
                });

                if (preference.preferred_taste) {
                    const preferredTaste = document.querySelector(`input[name="preferred_taste"][value="${String(preference.preferred_taste).toLowerCase()}"]`);
                    if (preferredTaste) preferredTaste.checked = true;
                }

                if (preference.price_preference) {
                    const priceInput = document.querySelector(`input[name="price_preference"][value="${String(preference.price_preference).toLowerCase()}"]`);
                    if (priceInput) priceInput.checked = true;
                }

                if (preference.spicy_level !== null && typeof preference.spicy_level !== 'undefined') {
                    const spicyInput = document.querySelector(`input[name="spicy_level"][value="${Number(preference.spicy_level)}"]`);
                    if (spicyInput) spicyInput.checked = true;
                }

                document.getElementById('favoriteIngredients').value = Array.isArray(preference.favorite_ingredients) ? preference.favorite_ingredients.join(', ') : '';
                document.getElementById('dislikedIngredients').value = Array.isArray(preference.disliked_ingredients) ? preference.disliked_ingredients.join(', ') : '';
            };

            try {
                const response = await fetch('/api/profile/preferences', { headers: authHeaders() });
                if (response.ok) {
                    const data = await response.json();
                    applyPreferenceData(data.preference || {});
                }
            } catch (error) {
                // Keep form empty until the user saves a valid set.
            }

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();
                errorBox.innerHTML = '';

                const payload = {
                    favorite_categories: collectCheckedValues('[data-name="favorite_categories"] input'),
                    favorite_food_types: collectCheckedValues('[data-name="favorite_food_types"] input'),
                    favorite_beverages: collectCheckedValues('[data-name="favorite_beverages"] input'),
                    preferred_taste: document.querySelector('input[name="preferred_taste"]:checked')?.value || null,
                    dietary_preferences: collectCheckedValues('[data-name="dietary_preferences"] input'),
                    price_preference: document.querySelector('input[name="price_preference"]:checked')?.value || null,
                    spicy_level: Number(document.querySelector('input[name="spicy_level"]:checked')?.value || 0),
                    favorite_ingredients: parseCsv(document.getElementById('favoriteIngredients').value),
                    disliked_ingredients: parseCsv(document.getElementById('dislikedIngredients').value),
                };

                try {
                    const response = await fetch('/api/profile/preferences', {
                        method: 'PUT',
                        headers: authHeaders(),
                        body: JSON.stringify(payload),
                    });
                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : (data.message || 'Unable to save preferences.');
                        errorBox.innerHTML = `<div class="error-item"><i class="bi bi-exclamation-circle-fill"></i><span>${firstError}</span></div>`;
                        return;
                    }

                    const savedPreference = data.preference || payload;
                    localStorage.setItem('savora_preferences_snapshot', JSON.stringify(savedPreference));
                    window.dispatchEvent(new CustomEvent('savora:preferences-updated', { detail: savedPreference }));
                    showToast('Preferences saved successfully.');
                    applyPreferenceData(savedPreference);
                    setTimeout(() => {
                        window.location.href = '/profile';
                    }, 500);
                } catch (error) {
                    errorBox.innerHTML = '<div class="error-item"><i class="bi bi-exclamation-circle-fill"></i><span>Unable to save preferences.</span></div>';
                }
            });
        });
    </script>
@endpush
