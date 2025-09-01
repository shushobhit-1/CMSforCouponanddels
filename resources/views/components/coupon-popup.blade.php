@props([
    'coupon' => null,
    'show' => false,
    'position' => 'center',
    'animation' => 'fadeIn',
    'delay' => 0,
    'style' => 'default'
])

@if($coupon)
<div id="coupon-popup-{{ $coupon->id }}" 
     class="coupon-popup {{ $show ? 'show' : '' }} position-{{ $position }} style-{{ $style }}"
     data-animation="{{ $animation }}"
     data-delay="{{ $delay }}"
     style="display: {{ $show ? 'block' : 'none' }};">
    
    <div class="coupon-popup-overlay" onclick="closeCouponPopup({{ $coupon->id }})"></div>
    
    <div class="coupon-popup-content">
        <!-- Close Button -->
        <button class="coupon-popup-close" onclick="closeCouponPopup({{ $coupon->id }})">
            <i class="fas fa-times"></i>
        </button>
        
        <!-- Coupon Header -->
        <div class="coupon-popup-header">
            <div class="store-logo">
                @if($coupon->store && $coupon->store->logo_url)
                    <img src="{{ $coupon->store->logo_url }}" alt="{{ $coupon->store->name }}" class="store-logo-img">
                @else
                    <div class="store-logo-placeholder">
                        <i class="fas fa-store"></i>
                    </div>
                @endif
            </div>
            
            <div class="coupon-info">
                <h3 class="coupon-title">{{ $coupon->title }}</h3>
                <p class="store-name">{{ $coupon->store->name ?? 'Unknown Store' }}</p>
                <div class="coupon-meta">
                    @if($coupon->discount_percentage)
                        <span class="discount-badge">{{ $coupon->discount_percentage }}% OFF</span>
                    @endif
                    @if($coupon->discount_amount)
                        <span class="discount-badge">₹{{ $coupon->discount_amount }} OFF</span>
                    @endif
                    @if($coupon->end_date)
                        <span class="expiry-badge">
                            <i class="fas fa-clock"></i>
                            Expires in {{ $coupon->remaining_days }} days
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Coupon Code Section -->
        <div class="coupon-code-section">
            <div class="coupon-code-display" id="coupon-code-{{ $coupon->id }}">
                <span class="code-placeholder">Click to reveal code</span>
                <span class="actual-code" style="display: none;">{{ $coupon->code }}</span>
            </div>
            
            <button class="reveal-code-btn" onclick="revealCouponCode({{ $coupon->id }})">
                <i class="fas fa-eye"></i>
                <span>Reveal Code</span>
            </button>
        </div>
        
        <!-- Coupon Description -->
        @if($coupon->description)
        <div class="coupon-description">
            <p>{{ $coupon->description }}</p>
        </div>
        @endif
        
        <!-- Terms and Conditions -->
        @if($coupon->terms_conditions)
        <div class="coupon-terms">
            <details>
                <summary>Terms & Conditions</summary>
                <div class="terms-content">
                    {!! nl2br(e($coupon->terms_conditions)) !!}
                </div>
            </details>
        </div>
        @endif
        
        <!-- Action Buttons -->
        <div class="coupon-actions">
            <a href="{{ $coupon->affiliate_link }}" 
               class="btn btn-primary get-deal-btn"
               onclick="trackCouponClick({{ $coupon->id }})"
               target="_blank"
               rel="noopener noreferrer">
                <i class="fas fa-external-link-alt"></i>
                Get Deal
            </a>
            
            <button class="btn btn-outline-secondary copy-code-btn" 
                    onclick="copyCouponCode({{ $coupon->id }})"
                    style="display: none;"
                    id="copy-btn-{{ $coupon->id }}">
                <i class="fas fa-copy"></i>
                Copy Code
            </button>
        </div>
        
        <!-- Share Section -->
        <div class="coupon-share-section">
            <h4>Share this deal</h4>
            <div class="share-icons">
                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}&quote={{ urlencode($coupon->title) }}" 
                   class="share-icon facebook"
                   target="_blank"
                   rel="noopener noreferrer"
                   onclick="trackShare('facebook', {{ $coupon->id }})">
                    <i class="fab fa-facebook-f"></i>
                </a>
                
                <!-- Twitter -->
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($coupon->title) }}&url={{ urlencode(request()->url()) }}" 
                   class="share-icon twitter"
                   target="_blank"
                   rel="noopener noreferrer"
                   onclick="trackShare('twitter', {{ $coupon->id }})">
                    <i class="fab fa-twitter"></i>
                </a>
                
                <!-- WhatsApp -->
                <a href="https://wa.me/?text={{ urlencode($coupon->title . ' ' . request()->url()) }}" 
                   class="share-icon whatsapp"
                   target="_blank"
                   rel="noopener noreferrer"
                   onclick="trackShare('whatsapp', {{ $coupon->id }})">
                    <i class="fab fa-whatsapp"></i>
                </a>
                
                <!-- Telegram -->
                <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($coupon->title) }}" 
                   class="share-icon telegram"
                   target="_blank"
                   rel="noopener noreferrer"
                   onclick="trackShare('telegram', {{ $coupon->id }})">
                    <i class="fab fa-telegram-plane"></i>
                </a>
                
                <!-- Email -->
                <a href="mailto:?subject={{ urlencode($coupon->title) }}&body={{ urlencode('Check out this amazing deal: ' . request()->url()) }}" 
                   class="share-icon email"
                   onclick="trackShare('email', {{ $coupon->id }})">
                    <i class="fas fa-envelope"></i>
                </a>
                
                <!-- Copy Link -->
                <button class="share-icon copy-link"
                        onclick="copyShareLink({{ $coupon->id }})"
                        title="Copy link">
                    <i class="fas fa-link"></i>
                </button>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="coupon-popup-footer">
            <p class="powered-by">
                Powered by <strong>{{ config('app.name', 'Coupon Deals CMS') }}</strong>
            </p>
        </div>
    </div>
</div>

<!-- JavaScript for Coupon Popup -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize popup with delay if specified
    const popup = document.getElementById('coupon-popup-{{ $coupon->id }}');
    const delay = {{ $delay }};
    
    if (delay > 0) {
        setTimeout(() => {
            showCouponPopup({{ $coupon->id }});
        }, delay * 1000);
    }
});

function showCouponPopup(couponId) {
    const popup = document.getElementById(`coupon-popup-${couponId}`);
    const animation = popup.dataset.animation;
    
    popup.style.display = 'block';
    
    // Add animation class
    setTimeout(() => {
        popup.classList.add('show', `animate-${animation}`);
    }, 10);
    
    // Track popup view
    trackCouponPopupView(couponId);
}

function closeCouponPopup(couponId) {
    const popup = document.getElementById(`coupon-popup-${couponId}`);
    popup.classList.remove('show');
    
    setTimeout(() => {
        popup.style.display = 'none';
    }, 300);
}

function revealCouponCode(couponId) {
    const codeDisplay = document.getElementById(`coupon-code-${couponId}`);
    const placeholder = codeDisplay.querySelector('.code-placeholder');
    const actualCode = codeDisplay.querySelector('.actual-code');
    const revealBtn = event.target.closest('.reveal-code-btn');
    const copyBtn = document.getElementById(`copy-btn-${couponId}`);
    
    // Hide placeholder and show actual code
    placeholder.style.display = 'none';
    actualCode.style.display = 'inline';
    
    // Change button text
    revealBtn.innerHTML = '<i class="fas fa-check"></i><span>Code Revealed</span>';
    revealBtn.classList.add('revealed');
    
    // Show copy button
    copyBtn.style.display = 'inline-block';
    
    // Track code revelation
    trackCouponCodeReveal(couponId);
}

function copyCouponCode(couponId) {
    const codeElement = document.getElementById(`coupon-code-${couponId}`);
    const code = codeElement.querySelector('.actual-code').textContent;
    
    navigator.clipboard.writeText(code).then(() => {
        const copyBtn = document.getElementById(`copy-btn-${couponId}`);
        const originalText = copyBtn.innerHTML;
        
        copyBtn.innerHTML = '<i class="fas fa-check"></i><span>Copied!</span>';
        copyBtn.classList.add('copied');
        
        setTimeout(() => {
            copyBtn.innerHTML = originalText;
            copyBtn.classList.remove('copied');
        }, 2000);
        
        // Track code copy
        trackCouponCodeCopy(couponId);
    });
}

function copyShareLink(couponId) {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const copyLinkBtn = event.target.closest('.copy-link');
        const originalIcon = copyLinkBtn.innerHTML;
        
        copyLinkBtn.innerHTML = '<i class="fas fa-check"></i>';
        copyLinkBtn.classList.add('copied');
        
        setTimeout(() => {
            copyLinkBtn.innerHTML = originalIcon;
            copyLinkBtn.classList.remove('copied');
        }, 2000);
    });
}

// Tracking functions
function trackCouponPopupView(couponId) {
    // Send analytics data to backend
    fetch('/api/coupons/' + couponId + '/track-popup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
}

function trackCouponCodeReveal(couponId) {
    fetch('/api/coupons/' + couponId + '/track-reveal', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
}

function trackCouponCodeCopy(couponId) {
    fetch('/api/coupons/' + couponId + '/track-copy', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
}

function trackCouponClick(couponId) {
    fetch('/api/coupons/' + couponId + '/track-click', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
}

function trackShare(platform, couponId) {
    fetch('/api/coupons/' + couponId + '/track-share', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ platform: platform })
    });
}
</script>
@endif