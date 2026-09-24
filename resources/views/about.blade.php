@extends('layouts.app')

@section('title', 'About Us - Savora Cafeteria')

@section('content')
    <div class="about-shell page-shell">
        <section class="about-hero reveal">
            <div class="about-copy">
                <span class="page-kicker">About Savora Cafeteria</span>
                <h1>Crafting Flavor, Guided by Intelligence</h1>
                <p>
                    Where the timeless soul of authentic coffee meets modern personalization — serving food and drinks
                    curated with purpose, passion, and precision.
                </p>
            </div>

            <div class="about-photo-wrap">
                <div class="about-photo"></div>
            </div>
        </section>

        <section class="about-story reveal">
            <div class="story-block">
                <span class="mini-label">Our essence</span>
                <h2>More than just a cafeteria, it’s your personal taste haven.</h2>
                <p>
                    Savora was founded on the belief that every guest deserves a memorable dining experience.
                    We blend warm hospitality with thoughtfully crafted food and beverages that feel personal,
                    comforting, and elevated.
                </p>

                <div class="feature-stack">
                    <div class="feature-row">
                        <span class="feature-icon"><i class="bi bi-lightning-charge"></i></span>
                        <div>
                            <h3>Intelligent Smart Matching</h3>
                            <p>High-dimensional embeddings help match your favorite flavors, moods, and routines.</p>
                        </div>
                    </div>

                    <div class="feature-row">
                        <span class="feature-icon"><i class="bi bi-check2-circle"></i></span>
                        <div>
                            <h3>100% Fresh & Authentic Quality</h3>
                            <p>From fresh ingredients to premium beans, each item is prepared with care and consistency.</p>
                        </div>
                    </div>

                    <div class="feature-row">
                        <span class="feature-icon"><i class="bi bi-clock-history"></i></span>
                        <div>
                            <h3>Fast, Thoughtful Service</h3>
                            <p>We balance comfort and speed so your experience feels effortless from first bite to last sip.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="story-visual">
                <div class="visual-tile tile-top">
                    <div class="tile-image tile-one"></div>
                </div>
                <div class="visual-tile tile-bottom">
                    <div class="tile-image tile-two"></div>
                    <div class="quote-box">
                        <span>“Every bite feels like it was made for you.”</span>
                        <small>Est. 2024</small>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats-row reveal">
            <div class="stat-box">
                <strong>98%</strong>
                <span>Taste Match Accuracy</span>
            </div>
            <div class="stat-box">
                <strong>45+</strong>
                <span>Artisan Dishes & Drinks</span>
            </div>
            <div class="stat-box">
                <strong>&lt; 15 min</strong>
                <span>Average Order Prep</span>
            </div>
            <div class="stat-box">
                <strong>100%</strong>
                <span>Fresh Daily Ingredients</span>
            </div>
        </section>

        <section class="process-section reveal">
            <div class="section-heading">
                <span class="mini-label">Smart technology behind every bite</span>
                <h2>How Our AI Personalization Works</h2>
            </div>

            <div class="process-grid">
                <article class="process-card">
                    <span class="step-number">01</span>
                    <h3>Profile & Taste DNA</h3>
                    <p>We learn your flavor profiles, spice levels, dietary preferences, and daily routines.</p>
                </article>

                <article class="process-card">
                    <span class="step-number">02</span>
                    <h3>Semantic Embeddings</h3>
                    <p>Our system maps ingredients, ratings, and preferences into aligned recommendation vectors.</p>
                </article>

                <article class="process-card">
                    <span class="step-number">03</span>
                    <h3>Match Calculation</h3>
                    <p>We compare your preferences against menu items to find highly relevant suggestions.</p>
                </article>

                <article class="process-card">
                    <span class="step-number">04</span>
                    <h3>Smart Combos & Budget</h3>
                    <p>Each recommendation balances taste, affordability, and your expected experience.</p>
                </article>
            </div>
        </section>
    </div>

    <footer class="brand-footer">
        <div class="brand-footer-inner">
            <div class="brand-mark">
                <span class="brand-font">Savora</span>
                <small>CAFETERIA</small>
            </div>
            <div class="footer-line"></div>
            <div class="footer-label">GOOD FOOD • GREAT MOOD</div>
        </div>
    </footer>
@endsection
