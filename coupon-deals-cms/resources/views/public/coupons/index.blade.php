@extends('layouts.public')

@section('title', 'Latest Coupons & Deals')
@section('meta_description', 'Discover the latest coupons and deals from top stores. Save money with exclusive discount codes and promotional offers.')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="display-4 fw-bold mb-4">
                    Save Big with <span class="text-warning">Exclusive Coupons</span>
                </h1>
                <p class="lead mb-4">
                    Discover thousands of verified coupons and deals from your favorite stores. 
                    Start saving money today with our exclusive discount codes!
                </p>
                <div class="d-flex gap-3">
                    <button class="btn btn-warning btn-lg px-4 py-2">
                        <i class="fas fa-search me-2"></i>Browse Deals
                    </button>
                    <button class="btn btn-outline-light btn-lg px-4 py-2">
                        <i class="fas fa-heart me-2"></i>My Favorites
                    </button>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="text-center">
                    <div class="position-relative">
                        <i class="fas fa-tags" style="font-size: 200px; opacity: 0.2;"></i>
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <div class="bg-white rounded-circle p-4 shadow">
                                <i class="fas fa-percentage text-primary" style="font-size: 60px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-outline-primary active" data-filter="all">All Coupons</button>
                    <button class="btn btn-outline-primary" data-filter="featured">Featured</button>
                    <button class="btn btn-outline-primary" data-filter="expiring">Expiring Soon</button>
                    <button class="btn btn-outline-primary" data-filter="new">New Today</button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-2 justify-content-md-end">
                    <select class="form-select" style="max-width: 200px;">
                        <option>Sort by Latest</option>
                        <option>Sort by Popular</option>
                        <option>Sort by Expiring</option>
                        <option>Sort by Discount</option>
                    </select>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary active" data-view="grid">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="btn btn-outline-secondary" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Coupons Grid -->
<section class="py-5">
    <div class="container">
        <div class="row" id="couponsGrid">
            @forelse($coupons as $coupon)
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="coupon-card card h-100">
                        <div class="card-body">
                            <!-- Store Info -->
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $coupon->store->logo_url ?? '/images/default-store.png' }}" 
                                     alt="{{ $coupon->store->name ?? 'Store' }}" 
                                     class="store-logo me-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $coupon->store->name ?? 'Store' }}</h6>
                                    <small class="text-muted">{{ $coupon->category->name ?? 'Category' }}</small>
                                </div>
                                @if($coupon->is_featured)
                                    <span class="badge bg-warning ms-auto">
                                        <i class="fas fa-star me-1"></i>Featured
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Coupon Title -->
                            <h5 class="card-title mb-3">{{ $coupon->title }}</h5>
                            
                            <!-- Discount Info -->
                            <div class="discount-info mb-3">
                                <div class="bg-light p-3 rounded text-center">
                                    <div class="fw-bold text-primary fs-4">{{ $coupon->discount_text }}</div>
                                    <small class="text-muted">Discount Available</small>
                                </div>
                            </div>
                            
                            <!-- Expiry Info -->
                            @if($coupon->expires_at)
                                <div class="expiry-info mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Expires: {{ $coupon->expires_at->format('M d, Y') }}
                                        <span class="text-danger ms-2">
                                            ({{ $coupon->expires_at->diffForHumans() }})
                                        </span>
                                    </small>
                                </div>
                            @endif
                            
                            <!-- Action Button -->
                            <div class="d-grid">
                                <button class="coupon-code btn btn-primary btn-lg pulse-animation"
                                        onclick="showCouponPopup(
                                            '{{ addslashes($coupon->title) }}',
                                            '{{ $coupon->code }}',
                                            '{{ $coupon->affiliate_url }}',
                                            '{{ addslashes($coupon->store->name ?? 'Store') }}'
                                        )">
                                    <i class="fas fa-ticket-alt me-2"></i>Get Coupon Code
                                </button>
                            </div>
                            
                            <!-- Additional Actions -->
                            <div class="d-flex justify-content-between mt-3">
                                <button class="btn btn-sm btn-outline-primary" onclick="toggleFavorite({{ $coupon->id }})">
                                    <i class="far fa-heart"></i> Save
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="shareCoupon({{ $coupon->id }})">
                                    <i class="fas fa-share-alt"></i> Share
                                </button>
                                <small class="text-muted align-self-center">
                                    <i class="fas fa-eye me-1"></i>{{ rand(100, 999) }} used
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-search" style="font-size: 80px; color: #dee2e6;"></i>
                        </div>
                        <h4 class="text-muted">No Coupons Found</h4>
                        <p class="text-muted">We couldn't find any coupons matching your criteria.</p>
                        <button class="btn btn-primary" onclick="window.location.reload()">
                            <i class="fas fa-refresh me-2"></i>Refresh Page
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($coupons->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</section>

<!-- Deal Cards Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="mb-0">
                    <i class="fas fa-fire text-danger me-2"></i>Hot Deals
                </h2>
                <p class="text-muted mb-0">Limited time offers you don't want to miss</p>
            </div>
            <div class="col-auto">
                <a href="#" class="btn btn-outline-primary">View All Deals</a>
            </div>
        </div>
        
        <div class="row">
            @for($i = 1; $i <= 3; $i++)
                <div class="col-lg-4 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="{{ $i * 100 }}">
                    <div class="coupon-card card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="/images/store-{{ $i }}.png" alt="Store" class="store-logo me-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">Sample Store {{ $i }}</h6>
                                    <small class="text-muted">Electronics</small>
                                </div>
                                <span class="badge bg-danger ms-auto">
                                    <i class="fas fa-fire me-1"></i>Hot
                                </span>
                            </div>
                            
                            <h5 class="card-title">Amazing Electronics Deal</h5>
                            
                            <div class="price-info mb-3">
                                <span class="h4 text-primary">$99</span>
                                <span class="text-muted text-decoration-line-through ms-2">$199</span>
                                <span class="badge bg-success ms-2">50% OFF</span>
                            </div>
                            
                            <div class="d-grid">
                                <button class="deal-btn btn btn-success btn-lg"
                                        onclick="showDealPopup(
                                            'Amazing Electronics Deal',
                                            'https://example.com/deal-{{ $i }}',
                                            'Sample Store {{ $i }}'
                                        )">
                                    <i class="fas fa-shopping-cart me-2"></i>Get Deal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>

<!-- Product Cards Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="mb-0">
                    <i class="fas fa-box text-info me-2"></i>Featured Products
                </h2>
                <p class="text-muted mb-0">Handpicked products with the best prices</p>
            </div>
            <div class="col-auto">
                <a href="#" class="btn btn-outline-info">View All Products</a>
            </div>
        </div>
        
        <div class="row">
            @for($i = 1; $i <= 4; $i++)
                <div class="col-lg-3 col-md-6 mb-4" data-aos="flip-left" data-aos-delay="{{ $i * 100 }}">
                    <div class="coupon-card card h-100">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="/images/product-{{ $i }}.jpg" alt="Product" 
                                     class="img-fluid rounded" style="height: 150px; object-fit: cover;">
                            </div>
                            
                            <h6 class="card-title">Sample Product {{ $i }}</h6>
                            
                            <div class="rating mb-2">
                                @for($j = 1; $j <= 5; $j++)
                                    <i class="fas fa-star text-warning"></i>
                                @endfor
                                <small class="text-muted ms-2">({{ rand(50, 500) }} reviews)</small>
                            </div>
                            
                            <div class="price-info mb-3">
                                <span class="h6 text-primary">${{ rand(50, 200) }}</span>
                                <small class="text-muted ms-2">Best Price</small>
                            </div>
                            
                            <div class="d-grid">
                                <button class="product-btn btn btn-info btn-sm"
                                        onclick="showProductPopup(
                                            'Sample Product {{ $i }}',
                                            'https://example.com/product-{{ $i }}',
                                            'Sample Store'
                                        )">
                                    <i class="fas fa-external-link-alt me-2"></i>Check Product
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h3 class="mb-3">Never Miss a Deal!</h3>
                <p class="mb-0">Subscribe to our newsletter and get the latest coupons and deals delivered to your inbox.</p>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="row g-2">
                    <div class="col">
                        <input type="email" class="form-control form-control-lg" placeholder="Enter your email">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-warning btn-lg px-4">
                            <i class="fas fa-paper-plane me-2"></i>Subscribe
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Additional JavaScript for favorites and sharing
    function toggleFavorite(couponId) {
        @auth
            fetch(`/api/favorites/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    type: 'coupon',
                    id: couponId
                })
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      Swal.fire({
                          icon: 'success',
                          title: data.favorited ? 'Added to Favorites' : 'Removed from Favorites',
                          toast: true,
                          position: 'top-end',
                          showConfirmButton: false,
                          timer: 2000
                      });
                  }
              });
        @else
            Swal.fire({
                icon: 'info',
                title: 'Login Required',
                text: 'Please login to save favorites',
                showCancelButton: true,
                confirmButtonText: 'Login',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("login") }}';
                }
            });
        @endauth
    }
    
    function shareCoupon(couponId) {
        if (navigator.share) {
            navigator.share({
                title: 'Check out this amazing coupon!',
                url: window.location.href
            });
        } else {
            // Fallback to copy link
            navigator.clipboard.writeText(window.location.href).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Link Copied!',
                    text: 'Share this link with your friends',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        }
    }
    
    // Filter functionality
    document.querySelectorAll('[data-filter]').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('[data-filter]').forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            // Here you would implement the actual filtering logic
            const filter = this.dataset.filter;
            console.log('Filtering by:', filter);
        });
    });
    
    // View toggle functionality
    document.querySelectorAll('[data-view]').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('[data-view]').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.dataset.view;
            const grid = document.getElementById('couponsGrid');
            
            if (view === 'list') {
                grid.classList.add('list-view');
            } else {
                grid.classList.remove('list-view');
            }
        });
    });
</script>
@endpush