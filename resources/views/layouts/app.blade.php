<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Savora Cafeteria')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    @stack('styles')
</head>
<body class="@yield('body_class')">

    <!-- loading -->
    <div id="pageLoader" class="page-loader">
        <img src="{{ asset('assets/images/logo-icon.png') }}" alt="Savora" class="loader-logo">
        <div class="loader-spinner"></div>
    </div>
    <header class="site-header reveal">
        <div class="header-inner">
            <button type="button" class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="bi bi-list"></i>
            </button>

            <a href="{{ route('home') }}" class="header-logo">
                <img src="{{ asset('assets/images/logo-icon.png') }}" alt="Savora">
                <div class="header-logo-text">
                    <span class="brand-font">Savora</span>
                    <small>CAFETERIA</small>
                </div>
            </a>

            <nav class="top-nav" aria-label="Main navigation">
                <a href="{{ route('home') }}" class="nav-link-pill {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('menu') }}" class="nav-link-pill {{ request()->routeIs('menu') ? 'active' : '' }}">Menu</a>
                <a href="{{ route('about') }}" class="nav-link-pill {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                <a href="{{ route('contact') }}" class="nav-link-pill {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
            </nav>

            <div class="header-search">
                <i class="bi bi-search"></i>
                <input type="text" id="headerSearch" placeholder="Search items, drinks or meals...">
                <button type="button" id="askAiBtn" class="btn-ask-ai">
                    <i class="bi bi-stars"></i> <span>Ask AI</span>
                </button>
            </div>

            <div class="header-actions">
                <a href="{{ route('cart') }}" class="header-icon-btn">
                    <i class="bi bi-cart3"></i>
                    <span id="cartBadge" class="badge-count d-none">0</span>
                </a>

                <div id="guestAction" class="d-none">
                    <a href="{{ route('login') }}" class="btn btn-savora btn-sm px-3">Login</a>
                </div>

                <div id="userAction" class="dropdown d-none">
                    <button class="avatar-btn dropdown-toggle" data-bs-toggle="dropdown">
                        <span id="userInitial">U</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('preferences') }}"><i class="bi bi-sliders me-2"></i>My Preferences</a></li>
                        <li><a class="dropdown-item" href="{{ route('orders') }}"><i class="bi bi-bag me-2"></i>My Orders</a></li>
                        <li><a class="dropdown-item" href="{{ route('favorites') }}"><i class="bi bi-heart me-2"></i>Favorites</a></li>
                        <li id="adminDashboardLink" class="d-none"><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger" id="logoutBtn"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></li>
                    </ul>
                </div>
            </div>

            <div class="header-search-mobile">
                <i class="bi bi-search"></i>
                <input type="text" id="headerSearchMobile" placeholder="Search...">
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="site-footer-inner">
            <div class="footer-brand">
                <div class="brand-mark-footer">
                    <span class="brand-font">Savora</span>
                    <small>CAFETERIA</small>
                </div>
                <p>Redefining the modern dining experience through authentic culinary excellence and intelligent personalization.</p>
            </div>

            <div class="footer-column">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('menu') }}">Menu</a></li>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Categories</h4>
                <ul>
                    <li><a href="{{ route('menu') }}">Artisan Coffee</a></li>
                    <li><a href="{{ route('menu') }}">Handcrafted Pizza</a></li>
                    <li><a href="{{ route('menu') }}">Gourmet Pasta</a></li>
                    <li><a href="{{ route('menu') }}">Cold Brews</a></li>
                </ul>
            </div>

            <div class="footer-column footer-column-newsletter">
                <h4>Stay Connected</h4>
                <p>Subscribe to receive seasonal specials, fresh drops, and curated tasting updates.</p>
                <form class="footer-form">
                    <input type="email" placeholder="Your email">
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-inner">
                <span>© 2026 Savora Cafeteria. All rights reserved.</span>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Ask AI Modal -->
    <div class="modal fade" id="askAiModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ask-ai-modal">
                <div class="modal-header border-0">
                    <h5 class="modal-title"><i class="bi bi-stars me-2"></i>Ask AI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Describe what you're craving, in Arabic or English.</p>
                    <div class="input-group mb-3">
                        <input type="text" id="aiQueryInput" class="form-control" placeholder="e.g. something cold and sweet">
                        <button class="btn btn-savora" id="aiQuerySubmit">
                            <span class="btn-label">Ask</span>
                            <span class="spinner-border spinner-border-sm d-none"></span>
                        </button>
                    </div>
                    <div id="aiQueryResult"></div>
                </div>
            </div>
        </div>
    </div>

        <div class="modal fade" id="chatbotModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ask-ai-modal chatbot-modal">
                <div class="modal-header border-0">
                    <h5 class="modal-title"><i class="bi bi-robot me-2"></i>Savora AI Chatbot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="chatbotMessages" class="chatbot-messages">
                        <div class="chatbot-bubble bot">
                            Hi! Ask me anything about the menu, your orders, or get a recommendation. 🍽️
                        </div>
                    </div>
                    <div class="input-group mt-3">
                        <input type="text" id="chatbotInput" class="form-control" placeholder="Type your message...">
                        <button class="btn btn-savora" id="chatbotSend">
                            <span class="btn-label"><i class="bi bi-send"></i></span>
                            <span class="spinner-border spinner-border-sm d-none"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/common.js') }}?v={{ filemtime(public_path('assets/js/common.js')) }}"></script>
    @stack('scripts')

    <script>
        window.addEventListener('load', () => {
            const loader = document.getElementById('pageLoader');
            if (!loader) return;

            const MIN_LOADER_TIME = 2000;  
            const elapsed = performance.now();
            const remaining = Math.max(0, MIN_LOADER_TIME - elapsed);

            setTimeout(() => {
                loader.classList.add('hide');
                setTimeout(() => loader.remove(), 500);
            }, remaining);
        });
    </script>
</body>
</html>
