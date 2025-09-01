<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', config('app.name', 'Coupon Deals CMS'))</title>
    <meta name="description" content="@yield('meta_description', 'Best deals, coupons and affiliate products')">
    <meta name="keywords" content="@yield('meta_keywords', 'deals, coupons, discounts, affiliate, products')">
    <meta name="author" content="@yield('meta_author', config('app.name'))">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'Best deals, coupons and affiliate products')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('twitter_description', 'Best deals, coupons and affiliate products')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/twitter-image.jpg'))">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    
    <!-- Theme CSS -->
    @if(file_exists(public_path('css/theme.css')))
        <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    @endif

    @stack('styles')

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#007bff">

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <!-- Header -->
    <header class="sticky-top">
        <!-- Top Bar -->
        <div class="top-bar bg-dark text-white py-2">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small><i class="fas fa-envelope me-2"></i>info@coupondeals.com</small>
                        <small class="ms-3"><i class="fas fa-phone me-2"></i>+1 234 567 8900</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="social-links">
                            <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" height="40" class="me-2">
                    CouponDeals
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- Search Bar -->
                    <div class="mx-auto d-lg-flex d-none">
                        <form class="d-flex search-form" role="search">
                            <div class="input-group">
                                <input class="form-control search-input" type="search" placeholder="Search for coupons, deals, stores..." aria-label="Search">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Right Menu -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i>Register
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('user.profile') }}">
                                        <i class="fas fa-user me-2"></i>Profile
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('user.favorites') }}">
                                        <i class="fas fa-heart me-2"></i>Favorites
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('user.notifications') }}">
                                        <i class="fas fa-bell me-2"></i>Notifications
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Mega Menu -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                <i class="fas fa-home me-1"></i>Home
                            </a>
                        </li>
                        <li class="nav-item dropdown mega-dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-th-large me-1"></i>Categories
                            </a>
                            <div class="dropdown-menu mega-menu">
                                <div class="container">
                                    <div class="row">
                                        <!-- Categories will be loaded dynamically -->
                                        <div class="col-md-3">
                                            <h6 class="dropdown-header">Fashion</h6>
                                            <a class="dropdown-item" href="#">Men's Fashion</a>
                                            <a class="dropdown-item" href="#">Women's Fashion</a>
                                            <a class="dropdown-item" href="#">Kids Fashion</a>
                                        </div>
                                        <div class="col-md-3">
                                            <h6 class="dropdown-header">Electronics</h6>
                                            <a class="dropdown-item" href="#">Mobile Phones</a>
                                            <a class="dropdown-item" href="#">Laptops</a>
                                            <a class="dropdown-item" href="#">Gaming</a>
                                        </div>
                                        <div class="col-md-3">
                                            <h6 class="dropdown-header">Home & Kitchen</h6>
                                            <a class="dropdown-item" href="#">Furniture</a>
                                            <a class="dropdown-item" href="#">Kitchen</a>
                                            <a class="dropdown-item" href="#">Home Decor</a>
                                        </div>
                                        <div class="col-md-3">
                                            <h6 class="dropdown-header">Health & Beauty</h6>
                                            <a class="dropdown-item" href="#">Skincare</a>
                                            <a class="dropdown-item" href="#">Makeup</a>
                                            <a class="dropdown-item" href="#">Health</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('coupons.index') }}">
                                <i class="fas fa-ticket-alt me-1"></i>Coupons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('deals.index') }}">
                                <i class="fas fa-fire me-1"></i>Deals
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products.index') }}">
                                <i class="fas fa-shopping-bag me-1"></i>Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('stores.index') }}">
                                <i class="fas fa-store me-1"></i>Stores
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">CouponDeals</h5>
                    <p class="text-light">Your one-stop destination for the best deals, coupons, and affiliate products. Save money while shopping your favorite brands.</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="{{ route('coupons.index') }}" class="text-light text-decoration-none">Coupons</a></li>
                        <li><a href="{{ route('deals.index') }}" class="text-light text-decoration-none">Deals</a></li>
                        <li><a href="{{ route('stores.index') }}" class="text-light text-decoration-none">Stores</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Fashion</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Electronics</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Home & Kitchen</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Health & Beauty</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Contact Us</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Privacy Policy</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Newsletter</h6>
                    <p class="text-light">Subscribe to get latest deals and offers</p>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your email">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} CouponDeals. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">Made with <i class="fas fa-heart text-danger"></i> for savvy shoppers</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="{{ asset('js/public.js') }}"></script>

    @stack('scripts')

    <!-- Initialize AOS -->
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>

    <!-- PWA Install Prompt -->
    <script>
        let deferredPrompt;
        
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            showInstallPromotion();
        });

        function showInstallPromotion() {
            // Show install promotion
            const installBanner = document.createElement('div');
            installBanner.className = 'alert alert-info alert-dismissible fade show position-fixed bottom-0 start-0 m-3';
            installBanner.style.zIndex = '9999';
            installBanner.innerHTML = `
                <i class="fas fa-mobile-alt me-2"></i>
                Install our app for a better experience!
                <button type="button" class="btn btn-sm btn-primary ms-2" onclick="installApp()">Install</button>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(installBanner);
        }

        function installApp() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                    }
                    deferredPrompt = null;
                });
            }
        }
    </script>
</body>
</html>