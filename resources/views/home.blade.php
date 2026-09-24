@extends('layouts.app')

@section('title', 'Home - Savora Cafeteria')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
@endpush

@section('content')
    <div class="home-shell page-shell">
        <section class="home-hero reveal">
            <div class="hero-copy">
                <span class="page-kicker"><i class="bi bi-sparkles"></i> Fresh &amp; healthy</span>
                <h1>Good Food Brings People Together</h1>
                <p>
                    Discover our delicious menu, personalized just for you with the power of AI and a café experience built around comfort.
                </p>
                <div class="page-actions">
                    <a href="{{ route('menu') }}" class="btn btn-savora">Explore Menu</a>
                    <a href="{{ route('about') }}" class="btn btn-savora-outline">About us</a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hero-slider" id="heroSlider" aria-live="polite"></div>
                <div class="hero-slider-dots" id="heroSliderDots"></div>
                <div class="floating-badge">
                    <strong>Fresh ingredients</strong>
                    <span>Better mood</span>
                </div>
                <div class="hero-arrows">
                    <button type="button" class="hero-arrow" data-direction="prev" aria-label="Previous slide"><i class="bi bi-chevron-left"></i></button>
                    <button type="button" class="hero-arrow" data-direction="next" aria-label="Next slide"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </section>

        <section class="categories-section reveal">
            <div class="section-header">
                <div>
                    <span class="mini-label">Shop by category</span>
                    <h2>Explore our menu</h2>
                </div>
                <a href="{{ route('menu') }}" class="view-link">View all categories <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="category-grid" id="homeCategoryGrid"></div>
        </section>

        <section class="featured-section reveal">
            <div class="section-header">
                <div>
                    <span class="mini-label">Featured items</span>
                    <h2>Our best picks</h2>
                </div>
                <a href="{{ route('menu') }}" class="view-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="featured-grid" id="featuredGrid"></div>
        </section>

        <section class="recommendation-block reveal">
            <div class="ai-spotlight">
                <div class="ai-spotlight-icon"><i class="bi bi-robot"></i></div>
                <div>
                    <span class="mini-label">Let our AI</span>
                    <h3>Recommend for you</h3>
                </div>
                <a href="{{ route('menu') }}" class="chat-link">Chat with AI <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="recommendation-grid" id="homeRecommendationGrid"></div>
        </section>

        <section class="why-choose-block reveal">
            <div class="section-header">
                <div>
                    <span class="mini-label">Why choose Savora?</span>
                    <h2>Made for flavor and feel-good moments</h2>
                </div>
            </div>

            <div class="reasons-grid">
                <article class="reason-card">
                    <div class="reason-icon"><i class="bi bi-leaf"></i></div>
                    <h3>Fresh ingredients</h3>
                    <p>We use only fresh, high-quality ingredients to keep every bite vibrant and satisfying.</p>
                </article>

                <article class="reason-card">
                    <div class="reason-icon"><i class="bi bi-basket3"></i></div>
                    <h3>Variety &amp; quality</h3>
                    <p>From hearty meals to tasty drinks, every item is selected for taste, balance, and comfort.</p>
                </article>

                <article class="reason-card">
                    <div class="reason-icon"><i class="bi bi-stars"></i></div>
                    <h3>AI-powered recommendations</h3>
                    <p>Get personalized suggestions based on your preferences, mood, and favorite flavors.</p>
                </article>

                <article class="reason-card">
                    <div class="reason-icon"><i class="bi bi-bag-check"></i></div>
                    <h3>Easy ordering</h3>
                    <p>Quick, smooth ordering experience from discovery to checkout with minimal effort.</p>
                </article>
            </div>

            <div class="stats-grid" id="statsGrid"></div>
        </section>

        <section class="testimonials-block reveal">
            <div class="section-header">
                <div>
                    <span class="mini-label">What our customers say</span>
                    <h2>Real people. Real flavor.</h2>
                </div>
                <a href="{{ route('menu') }}" class="view-link">View all testimonials <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="testimonials-grid">
                <article class="testimonial-card">
                    <p>“The food is fresh and the AI recommendations actually match what I love.”</p>
                    <div class="person-row">
                        <div class="person-avatar avatar-one">S</div>
                        <div>
                            <strong>Sarah Ahmed</strong>
                            <small>Customer</small>
                        </div>
                    </div>
                </article>

                <article class="testimonial-card highlight">
                    <p>“Great flavors, friendly service and a smooth ordering experience every time.”</p>
                    <div class="person-row">
                        <div class="person-avatar avatar-two">O</div>
                        <div>
                            <strong>Omar Hassan</strong>
                            <small>Customer</small>
                        </div>
                    </div>
                </article>

                <article class="testimonial-card">
                    <p>“The AI suggestions helped me find the perfect drink and dessert combo.”</p>
                    <div class="person-row">
                        <div class="person-avatar avatar-three">L</div>
                        <div>
                            <strong>Layla M.</strong>
                            <small>Customer</small>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section class="cta-banner reveal">
            <div>
                <h3>Ready to experience the future of café dining?</h3>
                <p>Discover smart recommendations, handcrafted favorites, and fresh comfort in one place.</p>
            </div>
            <a href="{{ route('menu') }}" class="btn btn-savora">Explore Menu</a>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/home.js') }}"></script>
@endpush
