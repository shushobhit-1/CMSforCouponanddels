@extends('layouts.public')

@section('title', 'Best Coupons & Discount Codes - Save Money Today')
@section('meta_description', 'Browse thousands of verified coupons and discount codes from top brands. Save money on your favorite products with our latest deals.')

@push('styles')
<style>
    .filters-sidebar {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        height: fit-content;
        position: sticky;
        top: 100px;
    }
    
    .coupon-grid {
        gap: 1.5rem;
    }
    
    .coupon-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    }
    
    .coupon-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .coupon-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        color: white;
        padding: 8px 15px;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: bold;
        z-index: 2;
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    }
    
    .store-logo {
        width: 70px;
        height: 70px;
        object-fit: contain;
        border-radius: 10px;
        background: white;
        padding: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .coupon-card:hover .store-logo {
        transform: scale(1.1);
    }
    
    .coupon-btn {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        border-radius: 25px;
        padding: 12px 25px;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .coupon-btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .coupon-btn:hover:before {
        left: 100%;
    }
    
    .coupon-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        color: white;
    }
    
    .coupon-stats {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .verified-badge {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: bold;
    }
    
    .filter-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .filter-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .filter-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }
    
    .filter-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
        cursor: pointer;
        transition: color 0.3s ease;
    }
    
    .filter-option:hover {
        color: #007bff;
    }
    
    .filter-count {
        background: #e9ecef;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .sort-dropdown {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 10px 15px;
        font-weight: 500;
    }
    
    .pagination {
        justify-content: center;
        margin-top: 3rem;
    }
    
    .page-link {
        border-radius: 10px;
        margin: 0 5px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .page-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 2rem;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        font-weight: bold;
        color: #007bff;
    }
    
    .results-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 0;
        margin-bottom: 3rem;
        border-radius: 0 0 30px 30px;
    }
</style>
@endpush

@section('content')
<!-- Results Header -->
<section class="results-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb text-white">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Coupons</li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold mb-3">Latest Coupons & Discount Codes</h1>
                <p class="lead mb-0">Save money with verified coupons from your favorite stores</p>
            </div>
            <div class="col-lg-4 text-end">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <div class="text-center">
                        <h3 class="mb-0" data-countup="{{ $coupons->total() }}">0</h3>
                        <small>Total Coupons</small>
                    </div>
                    <div class="text-center">
                        <h3 class="mb-0" data-countup="{{ $stores->count() }}">0</h3>
                        <small>Partner Stores</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="filters-sidebar">
                <h5 class="filter-title mb-4">
                    <i class="fas fa-filter me-2"></i>Filter Coupons
                </h5>
                
                <!-- Store Filter -->
                <div class="filter-section">
                    <h6 class="filter-title">Popular Stores</h6>
                    @foreach($stores->take(8) as $store)
                    <div class="filter-option">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2" name="stores[]" value="{{ $store->slug }}">
                            {{ $store->name }}
                        </label>
                        <span class="filter-count">{{ $store->coupons_count ?? 0 }}</span>
                    </div>
                    @endforeach
                    <a href="#" class="text-primary small text-decoration-none">View All Stores</a>
                </div>
                
                <!-- Category Filter -->
                <div class="filter-section">
                    <h6 class="filter-title">Categories</h6>
                    @foreach($categories->take(6) as $category)
                    <div class="filter-option">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2" name="categories[]" value="{{ $category->slug }}">
                            {{ $category->name }}
                        </label>
                        <span class="filter-count">{{ $category->coupons_count ?? 0 }}</span>
                    </div>
                    @endforeach
                </div>
                
                <!-- Coupon Type Filter -->
                <div class="filter-section">
                    <h6 class="filter-title">Coupon Type</h6>
                    <div class="filter-option">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input me-2" name="type" value="code">
                            Coupon Code
                        </label>
                        <span class="filter-count">{{ $coupons->where('type', 'code')->count() }}</span>
                    </div>
                    <div class="filter-option">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input me-2" name="type" value="deal">
                            Deal/Offer
                        </label>
                        <span class="filter-count">{{ $coupons->where('type', 'deal')->count() }}</span>
                    </div>
                </div>
                
                <!-- Discount Range -->
                <div class="filter-section">
                    <h6 class="filter-title">Discount Range</h6>
                    <div class="filter-option">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2" name="discount[]" value="0-25">
                            Up to 25% OFF
                        </label>
                    </div>
                    <div class="filter-option">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2" name="discount[]" value="25-50">
                            25% - 50% OFF
                        </label>
                    </div>
                    <div class="filter-option">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2" name="discount[]" value="50+">
                            50%+ OFF
                        </label>
                    </div>
                </div>
                
                <!-- Clear Filters -->
                <button class="btn btn-outline-secondary w-100 mt-3" id="clearFilters">
                    <i class="fas fa-times me-2"></i>Clear All Filters
                </button>
            </div>
        </div>
        
        <!-- Coupons Grid -->
        <div class="col-lg-9">
            <!-- Search and Sort -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="results-info">
                    <h5 class="mb-0">Showing {{ $coupons->count() }} of {{ $coupons->total() }} coupons</h5>
                    <small class="text-muted">Updated {{ now()->diffForHumans() }}</small>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <label class="form-label mb-0 fw-bold">Sort by:</label>
                    <select class="form-select sort-dropdown" style="width: auto;">
                        <option value="newest">Newest First</option>
                        <option value="popular">Most Popular</option>
                        <option value="expiry">Expiring Soon</option>
                        <option value="discount">Highest Discount</option>
                    </select>
                </div>
            </div>
            
            <!-- Coupons Grid -->
            <div class="row coupon-grid">
                @forelse($coupons as $coupon)
                <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="coupon-card card h-100">
                        <div class="coupon-badge">
                            @if($coupon->discount_percentage)
                                {{ $coupon->discount_percentage }}% OFF
                            @elseif($coupon->discount_amount)
                                ₹{{ $coupon->discount_amount }} OFF
                            @else
                                DEAL
                            @endif
                        </div>
                        
                        <div class="card-body">
                            <!-- Store Info -->
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $coupon->store->getFirstMediaUrl('logo') ?: 'https://via.placeholder.com/70x70?text=' . substr($coupon->store->name, 0, 1) }}" 
                                     alt="{{ $coupon->store->name }}" class="store-logo me-3">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $coupon->store->name }}</h6>
                                    <small class="text-muted">{{ $coupon->category->name ?? 'General' }}</small>
                                    @if($coupon->is_verified)
                                        <div class="verified-badge mt-1">
                                            <i class="fas fa-check-circle me-1"></i>Verified
                                        </div>
                                    @endif
                                </div>
                                <button class="btn btn-sm btn-outline-danger favorite-btn" 
                                        data-type="coupon" data-id="{{ $coupon->id }}"
                                        title="Add to favorites">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                            
                            <!-- Coupon Details -->
                            <h6 class="card-title fw-bold mb-2">{{ $coupon->title }}</h6>
                            <p class="card-text text-muted small mb-3">{{ Str::limit($coupon->description, 80) }}</p>
                            
                            <!-- Coupon Stats -->
                            <div class="coupon-stats mb-3">
                                <div class="stat-item">
                                    <i class="fas fa-eye"></i>
                                    <span>{{ number_format($coupon->views_count) }}</span>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-mouse-pointer"></i>
                                    <span>{{ number_format($coupon->clicks_count) }} used</span>
                                </div>
                                @if($coupon->expires_at)
                                <div class="stat-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $coupon->expires_at->diffForHumans() }}</span>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Action Button -->
                            <div class="d-flex justify-content-between align-items-center">
                                @if($coupon->type === 'code')
                                <button class="coupon-btn flex-grow-1 me-2" 
                                        onclick="showCouponPopup({{ $coupon->id }}, '{{ $coupon->title }}')">
                                    <i class="fas fa-ticket-alt me-2"></i>Get Code
                                </button>
                                @else
                                <a href="{{ $coupon->affiliate_url }}" target="_blank" 
                                   class="coupon-btn flex-grow-1 me-2 text-center text-decoration-none"
                                   onclick="trackAffiliateClick('{{ $coupon->affiliate_url }}')">
                                    <i class="fas fa-external-link-alt me-2"></i>Get Deal
                                </a>
                                @endif
                                <button class="btn btn-outline-primary btn-sm" title="Share coupon">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <img src="https://via.placeholder.com/200x150?text=No+Coupons" alt="No coupons" class="mb-3 opacity-50">
                    <h4 class="text-muted">No coupons found</h4>
                    <p class="text-muted">Try adjusting your filters or search criteria</p>
                    <a href="{{ route('coupons.index') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>Reset Filters
                    </a>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($coupons->hasPages())
            <div class="d-flex justify-content-center">
                {{ $coupons->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Newsletter Section -->
<section class="py-5 bg-primary text-white mt-5">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h3 class="fw-bold mb-3">Never Miss a Deal!</h3>
                <p class="mb-4">Get the latest coupons and exclusive offers delivered to your inbox</p>
                <form class="newsletter-form">
                    <div class="input-group input-group-lg">
                        <input type="email" class="form-control" placeholder="Enter your email">
                        <button class="btn btn-warning" type="submit">
                            <i class="fas fa-bell me-2"></i>Subscribe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/countup@1.8.2/dist/countUp.min.js"></script>

<script>
// Initialize counters
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

// Filter functionality
document.getElementById('clearFilters').addEventListener('click', function() {
    // Clear all checkboxes and radio buttons
    document.querySelectorAll('.filters-sidebar input[type="checkbox"], .filters-sidebar input[type="radio"]').forEach(input => {
        input.checked = false;
    });
    
    // Reload page without filters
    window.location.href = window.location.pathname;
});

// Sort functionality
document.querySelector('.sort-dropdown').addEventListener('change', function() {
    const url = new URL(window.location);
    url.searchParams.set('sort', this.value);
    window.location.href = url.toString();
});

// Enhanced coupon popup with better styling
function showCouponPopup(couponId, couponTitle) {
    // This will be handled by the global public.js file
    if (typeof window.showCouponPopup === 'function') {
        window.showCouponPopup(couponId, couponTitle);
    } else {
        // Fallback implementation
        Swal.fire({
            title: 'Coupon Code',
            html: `
                <div class="text-center">
                    <div class="coupon-code-display p-4 bg-primary text-white rounded-3 mb-3" style="cursor: pointer; font-size: 1.5rem; font-weight: bold;">
                        SAVE${couponId}0
                    </div>
                    <p>Click the code above to copy it!</p>
                    <div class="share-buttons mt-3">
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="shareOnFacebook()">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info me-2" onclick="shareOnTwitter()">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="shareOnWhatsApp()">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Go to Store',
            cancelButtonText: 'Close',
            confirmButtonColor: '#007bff'
        });
    }
}
</script>
@endpush