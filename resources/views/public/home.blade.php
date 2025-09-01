@extends('layouts.public')

@section('title', 'Best Deals, Coupons & Offers - Save Money on Your Favorite Brands')
@section('meta_description', 'Discover the best deals, coupons, and offers from top brands. Save money on fashion, electronics, home & kitchen, and more. Updated daily!')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 80vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .hero-particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }
    .hero-content {
        position: relative;
        z-index: 2;
    }
    .floating-stats {
        position: absolute;
        right: 10%;
        top: 20%;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 30px;
        color: white;
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    .stats-counter {
        font-size: 2.5rem;
        font-weight: bold;
        color: #FFD700;
    }
    .category-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        background: linear-gradient(45deg, #f8f9fa, #ffffff);
    }
    .category-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .category-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .coupon-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
    }
    .coupon-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    .coupon-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .store-logo {
        width: 80px;
        height: 80px;
        object-fit: contain;
        border-radius: 10px;
        background: white;
        padding: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .deal-card {
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        transition: all 0.3s ease;
    }
    .deal-card:hover {
        transform: scale(1.02);
    }
    .deal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(0,0,0,0.7), rgba(0,0,0,0.3));
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .deal-card:hover .deal-overlay {
        opacity: 1;
    }
    .newsletter-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }
    .newsletter-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .app-download {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .phone-mockup {
        max-width: 300px;
        transform: rotate(-5deg);
        animation: phone-float 4s ease-in-out infinite;
    }
    @keyframes phone-float {
        0%, 100% { transform: rotate(-5deg) translateY(0px); }
        50% { transform: rotate(-5deg) translateY(-20px); }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div id="particles-js" class="hero-particles"></div>
    
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-content text-white">
                    <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInUp">
                        Save <span class="text-warning">Big</span> on Every Purchase
                    </h1>
                    <p class="lead mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                        Discover thousands of exclusive deals, coupons, and offers from your favorite brands. 
                        Start saving money today!
                    </p>
                    
                    <!-- Search Bar -->
                    <div class="search-container mb-4 animate__animated animate__fadeInUp animate__delay-2s">
                        <form class="search-form">
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control rounded-start" placeholder="Search for stores, products, or deals...">
                                <select class="form-select" style="max-width: 150px;">
                                    <option value="">All Categories</option>
                                    <option value="fashion">Fashion</option>
                                    <option value="electronics">Electronics</option>
                                    <option value="home">Home & Kitchen</option>
                                    <option value="beauty">Health & Beauty</option>
                                </select>
                                <button class="btn btn-warning" type="submit">
                                    <i class="fas fa-search"></i> Search Deals
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Quick Stats -->
                    <div class="row text-center animate__animated animate__fadeInUp animate__delay-3s">
                        <div class="col-4">
                            <div class="stat-item">
                                <h3 class="text-warning mb-0" data-countup="1250">0</h3>
                                <small>Active Coupons</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <h3 class="text-warning mb-0" data-countup="500">0</h3>
                                <small>Partner Stores</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <h3 class="text-warning mb-0" data-countup="50000">0</h3>
                                <small>Happy Users</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Stats Card -->
    <div class="floating-stats d-none d-lg-block animate__animated animate__fadeInRight animate__delay-2s">
        <div class="text-center">
            <i class="fas fa-chart-line fa-2x mb-3 text-warning"></i>
            <div class="stats-counter" data-countup="2500000">0</div>
            <p class="mb-0">Money Saved This Month</p>
            <small class="text-warning">₹ and counting...</small>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold mb-3">Shop by Categories</h2>
            <p class="lead text-muted">Explore deals across all your favorite categories</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="category-card card h-100 text-center p-4">
                    <div class="category-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h6 class="fw-bold">Fashion</h6>
                    <small class="text-muted">250+ Deals</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="category-card card h-100 text-center p-4">
                    <div class="category-icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <h6 class="fw-bold">Electronics</h6>
                    <small class="text-muted">180+ Deals</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="category-card card h-100 text-center p-4">
                    <div class="category-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h6 class="fw-bold">Home & Kitchen</h6>
                    <small class="text-muted">320+ Deals</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="400">
                <div class="category-card card h-100 text-center p-4">
                    <div class="category-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h6 class="fw-bold">Health & Beauty</h6>
                    <small class="text-muted">150+ Deals</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="500">
                <div class="category-card card h-100 text-center p-4">
                    <div class="category-icon">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <h6 class="fw-bold">Gaming</h6>
                    <small class="text-muted">90+ Deals</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="600">
                <div class="category-card card h-100 text-center p-4">
                    <div class="category-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h6 class="fw-bold">Books</h6>
                    <small class="text-muted">75+ Deals</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Coupons Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-up">
            <div>
                <h2 class="display-5 fw-bold mb-2">Featured Coupons</h2>
                <p class="text-muted mb-0">Handpicked deals just for you</p>
            </div>
            <a href="{{ route('coupons.index') }}" class="btn btn-outline-primary">
                View All Coupons <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4">
            @for($i = 1; $i <= 6; $i++)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="coupon-card card h-100">
                    <div class="coupon-badge">{{ ['50% OFF', '₹500 OFF', 'FLAT 30%', 'BUY 1 GET 1', '₹200 OFF', 'MEGA SALE'][array_rand(['50% OFF', '₹500 OFF', 'FLAT 30%', 'BUY 1 GET 1', '₹200 OFF', 'MEGA SALE'])] }}</div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://via.placeholder.com/60x60?text=Logo" alt="Store" class="store-logo me-3">
                            <div>
                                <h6 class="fw-bold mb-1">{{ ['Amazon', 'Flipkart', 'Myntra', 'Nykaa', 'Ajio', 'Zomato'][array_rand(['Amazon', 'Flipkart', 'Myntra', 'Nykaa', 'Ajio', 'Zomato'])] }}</h6>
                                <small class="text-muted">{{ ['Fashion', 'Electronics', 'Beauty', 'Food', 'Home', 'Travel'][array_rand(['Fashion', 'Electronics', 'Beauty', 'Food', 'Home', 'Travel'])] }}</small>
                            </div>
                        </div>
                        <h6 class="card-title">{{ ['Mega Fashion Sale', 'Electronics Bonanza', 'Beauty Essentials', 'Food Fiesta', 'Home Makeover', 'Travel Deals'][array_rand(['Mega Fashion Sale', 'Electronics Bonanza', 'Beauty Essentials', 'Food Fiesta', 'Home Makeover', 'Travel Deals'])] }}</h6>
                        <p class="card-text text-muted small mb-3">Get amazing discounts on your favorite products. Limited time offer!</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">Expires: Dec 31, 2024</small>
                            </div>
                            <button class="btn btn-primary btn-sm coupon-btn" onclick="showCouponPopup('SAVE{{ $i }}0')">
                                <i class="fas fa-ticket-alt me-1"></i>Get Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- Hot Deals Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-up">
            <div>
                <h2 class="display-5 fw-bold mb-2">🔥 Hot Deals</h2>
                <p class="text-muted mb-0">Limited time offers you can't miss</p>
            </div>
            <a href="{{ route('deals.index') }}" class="btn btn-outline-danger">
                View All Deals <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4">
            @for($i = 1; $i <= 4; $i++)
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="deal-card card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="https://via.placeholder.com/300x200?text=Deal+{{ $i }}" class="img-fluid rounded-start h-100 object-fit-cover" alt="Deal">
                            <div class="deal-overlay"></div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-danger">Limited Time</span>
                                    <small class="text-muted">{{ rand(10, 50) }} left</small>
                                </div>
                                <h6 class="card-title fw-bold">{{ ['Smartphone Mega Sale', 'Laptop Clearance', 'Fashion Bonanza', 'Home Appliances'][array_rand(['Smartphone Mega Sale', 'Laptop Clearance', 'Fashion Bonanza', 'Home Appliances'])] }}</h6>
                                <p class="card-text text-muted small">Amazing deals on premium products with up to 70% discount.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="h5 text-success fw-bold">₹{{ number_format(rand(5000, 50000)) }}</span>
                                        <small class="text-muted text-decoration-line-through ms-2">₹{{ number_format(rand(60000, 100000)) }}</small>
                                    </div>
                                    <button class="btn btn-success btn-sm">
                                        <i class="fas fa-bolt me-1"></i>Get Deal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- Popular Stores Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold mb-3">Popular Stores</h2>
            <p class="lead text-muted">Shop from your favorite brands</p>
        </div>

        <div class="row justify-content-center">
            @foreach(['Amazon', 'Flipkart', 'Myntra', 'Nykaa', 'Ajio', 'Swiggy', 'Zomato', 'BookMyShow'] as $index => $store)
            <div class="col-lg-3 col-md-4 col-6 text-center mb-4" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                <div class="store-card p-4 rounded-3 bg-white shadow-sm h-100 d-flex flex-column justify-content-center">
                    <img src="https://via.placeholder.com/120x80?text={{ $store }}" alt="{{ $store }}" class="img-fluid mb-3 mx-auto d-block" style="max-height: 80px;">
                    <h6 class="fw-bold mb-2">{{ $store }}</h6>
                    <small class="text-muted">{{ rand(10, 50) }} Active Offers</small>
                    <a href="#" class="btn btn-outline-primary btn-sm mt-3">Shop Now</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- App Download Section -->
<section class="app-download py-5 text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="display-5 fw-bold mb-4">Get the Mobile App</h2>
                <p class="lead mb-4">Download our app and never miss a deal. Get instant notifications for new coupons and exclusive mobile-only offers.</p>
                
                <div class="app-features mb-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-bell fa-2x text-warning me-3"></i>
                                <div>
                                    <h6 class="mb-1">Push Notifications</h6>
                                    <small>Get instant alerts for new deals</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-heart fa-2x text-warning me-3"></i>
                                <div>
                                    <h6 class="mb-1">Save Favorites</h6>
                                    <small>Keep track of your favorite stores</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="app-buttons">
                    <a href="#" class="btn btn-dark btn-lg me-3 mb-2">
                        <i class="fab fa-apple me-2"></i>App Store
                    </a>
                    <a href="#" class="btn btn-success btn-lg mb-2">
                        <i class="fab fa-google-play me-2"></i>Google Play
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center" data-aos="fade-left">
                <img src="https://via.placeholder.com/300x600?text=App+Screenshot" alt="Mobile App" class="phone-mockup img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section py-5 text-white position-relative">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8" data-aos="fade-up">
                <i class="fas fa-envelope fa-3x mb-4 text-warning"></i>
                <h2 class="display-5 fw-bold mb-4">Never Miss a Deal</h2>
                <p class="lead mb-4">Subscribe to our newsletter and get the best deals delivered straight to your inbox. No spam, just savings!</p>
                
                <form class="newsletter-form">
                    <div class="input-group input-group-lg justify-content-center">
                        <input type="email" class="form-control rounded-start" placeholder="Enter your email address" style="max-width: 400px;">
                        <button class="btn btn-warning" type="submit">
                            <i class="fas fa-paper-plane me-2"></i>Subscribe Now
                        </button>
                    </div>
                </form>
                
                <small class="d-block mt-3 opacity-75">Join 50,000+ savvy shoppers already subscribed</small>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/countup@1.8.2/dist/countUp.min.js"></script>

<script>
// Particles.js configuration
particlesJS('particles-js', {
    particles: {
        number: { value: 80, density: { enable: true, value_area: 800 } },
        color: { value: '#ffffff' },
        shape: { type: 'circle' },
        opacity: { value: 0.5, random: false },
        size: { value: 3, random: true },
        line_linked: { enable: true, distance: 150, color: '#ffffff', opacity: 0.4, width: 1 },
        move: { enable: true, speed: 6, direction: 'none', random: false, straight: false, out_mode: 'out', bounce: false }
    },
    interactivity: {
        detect_on: 'canvas',
        events: { onhover: { enable: true, mode: 'repulse' }, onclick: { enable: true, mode: 'push' }, resize: true },
        modes: { grab: { distance: 400, line_linked: { opacity: 1 } }, bubble: { distance: 400, size: 40, duration: 2, opacity: 8, speed: 3 }, repulse: { distance: 200, duration: 0.4 }, push: { particles_nb: 4 }, remove: { particles_nb: 2 } }
    },
    retina_detect: true
});

// CountUp animation
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('[data-countup]');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const endVal = parseInt(target.getAttribute('data-countup'));
                const countUp = new CountUp(target, 0, endVal, 0, 2);
                countUp.start();
                observer.unobserve(target);
            }
        });
    });
    
    counters.forEach(counter => observer.observe(counter));
});

// Coupon popup function
function showCouponPopup(code) {
    Swal.fire({
        title: 'Coupon Code Revealed!',
        html: `
            <div class="text-center">
                <div class="coupon-code-display p-3 bg-light rounded mb-3">
                    <h3 class="text-primary fw-bold">${code}</h3>
                    <small class="text-muted">Click to copy</small>
                </div>
                <p>Use this code at checkout to get your discount!</p>
                <div class="share-buttons mt-3">
                    <a href="#" class="btn btn-sm btn-outline-primary me-2" onclick="shareOnFacebook()">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-info me-2" onclick="shareOnTwitter()">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-success" onclick="shareOnWhatsApp()">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Copy Code & Shop',
        cancelButtonText: 'Close',
        confirmButtonColor: '#007bff'
    }).then((result) => {
        if (result.isConfirmed) {
            navigator.clipboard.writeText(code);
            Swal.fire('Copied!', 'Coupon code copied to clipboard', 'success');
        }
    });
}

// Newsletter subscription
document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('input[type="email"]').value;
    
    if (email) {
        Swal.fire({
            title: 'Success!',
            text: 'You have been subscribed to our newsletter',
            icon: 'success',
            confirmButtonColor: '#007bff'
        });
        this.reset();
    }
});
</script>
@endpush