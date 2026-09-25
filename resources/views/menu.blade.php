@extends('layouts.app')

@section('title', 'Menu - Savora Cafeteria')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}">
@endpush

@section('content')
<div class="menu-page">

    <!-- Category Tabs -->
    <div class="category-tabs-wrap reveal">
        <div class="category-tabs" id="categoryTabs">
            <button class="cat-tab active" data-id="" data-type="">
                <i class="bi bi-grid"></i> All
            </button>
            <!-- الباقي بيتحط ديناميك -->
        </div>
    </div>

    <div class="menu-layout">

        <button type="button" class="filters-toggle-btn" id="filtersToggleBtn">
            <i class="bi bi-sliders"></i> Filters
        </button>

        <div class="filters-overlay" id="filtersOverlay"></div>

        <!-- Filters Sidebar -->
        <aside class="filters-sidebar reveal" id="filtersSidebar">
            <button type="button" class="filters-close-btn" id="filtersCloseBtn">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="filters-head">
                <h6>Filters</h6>
                <button type="button" id="clearFilters" class="btn-clear">Clear all</button>
            </div>

            <div class="filter-group">
                <label>Type</label>
                <div class="type-toggle" id="typeToggle">
                    <button class="active" data-type="">All</button>
                    <button data-type="food">Food</button>
                    <button data-type="beverage">Drinks</button>
                </div>
            </div>

            <div class="filter-group">
                <label>Price Range</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="range" id="minPrice" min="0" max="100000" value="0" step="5" class="form-range">
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <input type="range" id="maxPrice" min="0" max="100000" value="100000" step="5" class="form-range">
                </div>
                <div class="d-flex justify-content-between small text-muted">
                    <span id="minPriceLabel">0 EGP</span>
                    <span id="maxPriceLabel">100000 EGP</span>
                </div>
            </div>

            <div class="filter-group">
                <label>Spicy Level</label>
                <div class="spicy-levels" id="spicyLevels">
                    <button data-level="">Any</button>
                    <button data-level="0">0</button>
                    <button data-level="1">1</button>
                    <button data-level="2">2</button>
                    <button data-level="3">3</button>
                    <button data-level="4">4</button>
                    <button data-level="5">5</button>
                </div>
                <small class="text-muted">Applies to food items only</small>
            </div>

            <div class="filter-group">
                <label for="maxCalories">Calories (Max)</label>
                <input type="number" id="maxCalories" class="form-control form-control-sm" placeholder="Any">
            </div>

            <div class="filter-group d-flex justify-content-between align-items-center">
                <label class="mb-0">Available Only</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="availableOnly">
                </div>
            </div>

            <div class="filter-group">
                <label for="sortBy">Sort By</label>
                <select id="sortBy" class="form-select form-select-sm">
                    <option value="newest">Newest</option>
                    <option value="name">Name</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                </select>
            </div>

            <div class="filters-footer">
                <p class="fst-italic">Good food brings people together</p>
            </div>
        </aside>

        <!-- Main content -->
        <div class="menu-main">

            <!-- Recommended -->
            <section id="recommendedSection" class="recommended-section reveal d-none">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-0"><i class="bi bi-stars text-warning"></i> Recommended for You</h5>
                        <small class="text-muted">Based on your preferences and past orders</small>
                    </div>
                    <button class="btn-view-all" id="viewAllRecommended">View All <i class="bi bi-arrow-right"></i></button>
                </div>

                <div class="recommended-carousel-wrap">
                    <button class="carousel-arrow left" id="recPrev"><i class="bi bi-chevron-left"></i></button>
                    <div class="recommended-carousel" id="recommendedCarousel"></div>
                    <button class="carousel-arrow right" id="recNext"><i class="bi bi-chevron-right"></i></button>
                </div>
            </section>

            <!-- Grid -->
            <section class="our-menu-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Our Menu</h5>
                    <small class="text-muted" id="resultsCount">Loading...</small>
                </div>

                <div class="menu-grid" id="menuGrid"></div>

                <div id="noResults" class="no-results d-none reveal">
                    <i class="bi bi-search"></i>
                    <h6>No items found</h6>
                    <p class="text-muted">Try adjusting your filters or search for something else.</p>
                    <button class="btn btn-outline-secondary btn-sm" id="clearFiltersEmpty">Clear Filters</button>
                </div>

                <div class="pagination-wrap" id="paginationWrap"></div>
            </section>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/menu.js') }}"></script>
@endpush
