<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Coupon Deals CMS'))</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Best deals, coupons and affiliate products from top stores')">
    <meta name="keywords" content="@yield('meta_keywords', 'deals, coupons, discounts, affiliate, offers')">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'Best deals and coupons')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#007bff">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --font-family: 'Inter', sans-serif;
        }
        
        body {
            font-family: var(--font-family);
            line-height: 1.6;
        }
        
        /* Share Icons Circular */
        .share-icons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
        
        .share-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .share-icon:hover {
            transform: scale(1.1);
            color: white;
        }
        
        .share-icon.facebook { background: linear-gradient(45deg, #1877F2, #42a5f5); }
        .share-icon.twitter { background: linear-gradient(45deg, #1DA1F2, #42a5f5); }
        .share-icon.whatsapp { background: linear-gradient(45deg, #25D366, #66bb6a); }
        .share-icon.telegram { background: linear-gradient(45deg, #0088cc, #42a5f5); }
        .share-icon.copy { background: linear-gradient(45deg, #6c757d, #9e9e9e); }
        
        /* Coupon Card Styles */
        .coupon-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        
        .coupon-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .coupon-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        }
        
        .coupon-code {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            letter-spacing: 1px;
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .coupon-code:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }
        
        .deal-btn, .product-btn {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        
        .deal-btn:hover, .product-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(40,167,69,0.3);
            color: white;
        }
        
        /* Mega Menu Styles */
        .mega-menu {
            position: static;
        }
        
        .mega-menu .dropdown-menu {
            width: 100%;
            border-radius: 0;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Store Logo */
        .store-logo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .share-icons {
                flex-wrap: wrap;
            }
            
            .coupon-card {
                margin-bottom: 20px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">
                <i class="fas fa-tags me-2"></i>{{ config('app.name', 'CouponDeals') }}
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Mega Menu -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown mega-menu">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-th-large me-1"></i>Categories
                        </a>
                        <div class="dropdown-menu p-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <h6 class="dropdown-header">Electronics</h6>
                                    <a class="dropdown-item" href="#">Smartphones</a>
                                    <a class="dropdown-item" href="#">Laptops</a>
                                    <a class="dropdown-item" href="#">Tablets</a>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="dropdown-header">Fashion</h6>
                                    <a class="dropdown-item" href="#">Men's Clothing</a>
                                    <a class="dropdown-item" href="#">Women's Clothing</a>
                                    <a class="dropdown-item" href="#">Shoes</a>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="dropdown-header">Home & Kitchen</h6>
                                    <a class="dropdown-item" href="#">Appliances</a>
                                    <a class="dropdown-item" href="#">Furniture</a>
                                    <a class="dropdown-item" href="#">Decor</a>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="dropdown-header">Travel</h6>
                                    <a class="dropdown-item" href="#">Hotels</a>
                                    <a class="dropdown-item" href="#">Flights</a>
                                    <a class="dropdown-item" href="#">Car Rentals</a>
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
                        <a class="nav-link" href="#">
                            <i class="fas fa-fire me-1"></i>Deals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-box me-1"></i>Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-store me-1"></i>Stores
                        </a>
                    </li>
                </ul>
                
                <!-- Search -->
                <div class="d-flex me-3">
                    <div class="input-group">
                        <input type="search" class="form-control" placeholder="Search deals...">
                        <button class="btn btn-outline-primary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <!-- User Menu -->
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li><a class="dropdown-item" href="#">Favorites</a></li>
                                <li><a class="dropdown-item" href="#">Notifications</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5 class="mb-3">{{ config('app.name') }}</h5>
                    <p class="text-muted">Your ultimate destination for the best deals, coupons, and affiliate products from top stores worldwide.</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">About Us</a></li>
                        <li><a href="#" class="text-muted">Contact</a></li>
                        <li><a href="#" class="text-muted">Privacy Policy</a></li>
                        <li><a href="#" class="text-muted">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Electronics</a></li>
                        <li><a href="#" class="text-muted">Fashion</a></li>
                        <li><a href="#" class="text-muted">Home & Kitchen</a></li>
                        <li><a href="#" class="text-muted">Travel</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3">Newsletter</h6>
                    <p class="text-muted">Subscribe to get the latest deals and offers.</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email">
                        <button class="btn btn-primary" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0 text-muted">Made with ❤️ for better deals</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });
        
        // Coupon Popup Function
        function showCouponPopup(title, code, affiliate_url, store_name) {
            const shareUrl = window.location.href;
            const shareText = `Check out this amazing coupon: ${title}`;
            
            Swal.fire({
                title: title,
                html: `
                    <div class="text-center">
                        <div class="mb-3">
                            <p class="text-muted">Use this coupon code at ${store_name}</p>
                        </div>
                        
                        <div class="coupon-code-container mb-4">
                            <div class="d-flex align-items-center justify-content-center">
                                <input type="text" class="form-control text-center fw-bold" 
                                       id="couponCode" value="${code}" readonly 
                                       style="font-size: 18px; max-width: 200px;">
                                <button class="btn btn-outline-primary ms-2" onclick="copyCouponCode()" id="copyBtn">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="share-icons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}" 
                               target="_blank" class="share-icon facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}" 
                               target="_blank" class="share-icon twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text=${encodeURIComponent(shareText + ' ' + shareUrl)}" 
                               target="_blank" class="share-icon whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareText)}" 
                               target="_blank" class="share-icon telegram">
                                <i class="fab fa-telegram-plane"></i>
                            </a>
                            <button onclick="copyShareLink()" class="share-icon copy">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Go to Store',
                cancelButtonText: 'Close',
                confirmButtonColor: '#007bff',
                customClass: {
                    popup: 'animate__animated animate__zoomIn'
                },
                didOpen: () => {
                    // Track coupon view
                    trackCouponClick(code);
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Track affiliate click
                    trackAffiliateClick(affiliate_url);
                    window.open(affiliate_url, '_blank');
                }
            });
        }
        
        function copyCouponCode() {
            const codeInput = document.getElementById('couponCode');
            codeInput.select();
            document.execCommand('copy');
            
            const copyBtn = document.getElementById('copyBtn');
            copyBtn.innerHTML = '<i class="fas fa-check"></i>';
            copyBtn.classList.remove('btn-outline-primary');
            copyBtn.classList.add('btn-success');
            
            setTimeout(() => {
                copyBtn.innerHTML = '<i class="fas fa-copy"></i>';
                copyBtn.classList.remove('btn-success');
                copyBtn.classList.add('btn-outline-primary');
            }, 2000);
            
            Swal.showValidationMessage('Coupon code copied to clipboard!');
            setTimeout(() => {
                Swal.resetValidationMessage();
            }, 2000);
        }
        
        function copyShareLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                Swal.showValidationMessage('Link copied to clipboard!');
                setTimeout(() => {
                    Swal.resetValidationMessage();
                }, 2000);
            });
        }
        
        function trackCouponClick(code) {
            fetch('/api/track-coupon-click', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    coupon_code: code,
                    user_agent: navigator.userAgent,
                    referrer: document.referrer
                })
            });
        }
        
        function trackAffiliateClick(url) {
            fetch('/api/track-affiliate-click', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    affiliate_url: url,
                    user_agent: navigator.userAgent,
                    referrer: document.referrer
                })
            });
        }
        
        // Deal Button Click
        function showDealPopup(title, affiliate_url, store_name) {
            Swal.fire({
                title: title,
                text: `This deal is available at ${store_name}`,
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Get Deal',
                cancelButtonText: 'Close',
                confirmButtonColor: '#28a745',
                customClass: {
                    popup: 'animate__animated animate__bounceIn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    trackAffiliateClick(affiliate_url);
                    window.open(affiliate_url, '_blank');
                }
            });
        }
        
        // Product Button Click
        function showProductPopup(title, affiliate_url, store_name) {
            Swal.fire({
                title: title,
                text: `View this product at ${store_name}`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Check Product',
                cancelButtonText: 'Close',
                confirmButtonColor: '#17a2b8',
                customClass: {
                    popup: 'animate__animated animate__fadeInUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    trackAffiliateClick(affiliate_url);
                    window.open(affiliate_url, '_blank');
                }
            });
        }
        
        // Initialize tooltips
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
    
    @stack('scripts')
</body>
</html>