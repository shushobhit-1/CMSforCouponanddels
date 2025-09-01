<!-- Coupon Popup Modal -->
<div class="coupon-popup" id="couponPopup" style="display: none;">
    <div class="coupon-popup-content">
        <!-- Close Button -->
        <button class="coupon-popup-close" onclick="closeCouponPopup()">
            <i class="fas fa-times"></i>
        </button>
        
        <!-- Popup Header -->
        <div class="coupon-popup-header">
            <h3 class="coupon-popup-title" id="popupTitle"></h3>
            <p class="coupon-popup-store" id="popupStore"></p>
        </div>
        
        <!-- Coupon Code (Hidden by default) -->
        <div class="coupon-popup-code hidden" id="popupCode">
            <span id="couponCodeText"></span>
        </div>
        
        <!-- Action Buttons -->
        <div class="coupon-popup-actions">
            <button class="coupon-popup-btn primary" onclick="revealCouponCode()" id="revealBtn">
                <i class="fas fa-eye"></i>
                Reveal Code
            </button>
            
            <a href="#" class="coupon-popup-btn secondary" id="getDealBtn" target="_blank" onclick="trackCouponClick()">
                <i class="fas fa-external-link-alt"></i>
                Get Deal
            </a>
            
            <button class="coupon-popup-btn outline" onclick="copyCouponCode()" id="copyBtn" style="display: none;">
                <i class="fas fa-copy"></i>
                Copy Code
            </button>
        </div>
        
        <!-- Upvote and Like System -->
        <div class="vote-system">
            <button class="vote-btn" id="upvoteBtn" onclick="voteCoupon('upvote')">
                <i class="fas fa-thumbs-up vote-icon"></i>
                <span class="vote-count" id="upvoteCount">0</span>
            </button>
            
            <button class="vote-btn" id="downvoteBtn" onclick="voteCoupon('downvote')">
                <i class="fas fa-thumbs-down vote-icon"></i>
                <span class="vote-count" id="downvoteCount">0</span>
            </button>
            
            <button class="vote-btn" id="likeBtn" onclick="voteCoupon('like')">
                <i class="fas fa-heart vote-icon"></i>
                <span class="vote-count" id="likeCount">0</span>
            </button>
            
            <button class="vote-btn" id="dislikeBtn" onclick="voteCoupon('dislike')">
                <i class="fas fa-heart-broken vote-icon"></i>
                <span class="vote-count" id="dislikeCount">0</span>
            </button>
        </div>
        
        <!-- Share Icons -->
        <div class="coupon-share-icons">
            <h6>Share This Coupon</h6>
            <div class="coupon-share-buttons">
                <a href="#" class="share-btn facebook" onclick="shareCoupon('facebook')" title="Share on Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                
                <a href="#" class="share-btn twitter" onclick="shareCoupon('twitter')" title="Share on Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                
                <a href="#" class="share-btn whatsapp" onclick="shareCoupon('whatsapp')" title="Share on WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                
                <a href="#" class="share-btn telegram" onclick="shareCoupon('telegram')" title="Share on Telegram">
                    <i class="fab fa-telegram-plane"></i>
                </a>
                
                <a href="#" class="share-btn email" onclick="shareCoupon('email')" title="Share via Email">
                    <i class="fas fa-envelope"></i>
                </a>
                
                <button class="share-btn copy" onclick="copyCouponLink()" title="Copy Link">
                    <i class="fas fa-link"></i>
                </button>
            </div>
        </div>
        
        <!-- Coupon Details -->
        <div class="coupon-details mt-3">
            <div class="row text-center">
                <div class="col-6">
                    <small class="text-muted">Expires</small>
                    <p class="mb-0 fw-bold" id="popupExpiry"></p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Used</small>
                    <p class="mb-0 fw-bold" id="popupUsage"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentCoupon = null;
let couponRevealed = false;

// Function to open coupon popup
function openCouponPopup(coupon) {
    currentCoupon = coupon;
    couponRevealed = false;
    
    // Populate popup content
    document.getElementById('popupTitle').textContent = coupon.title;
    document.getElementById('popupStore').textContent = coupon.store_name;
    document.getElementById('couponCodeText').textContent = coupon.code;
    document.getElementById('popupExpiry').textContent = coupon.end_date ? new Date(coupon.end_date).toLocaleDateString() : 'No expiry';
    document.getElementById('popupUsage').textContent = `${coupon.used_count || 0}/${coupon.usage_limit || '∞'}`;
    
    // Set affiliate link
    const getDealBtn = document.getElementById('getDealBtn');
    getDealBtn.href = coupon.affiliate_link || '#';
    
    // Reset popup state
    document.getElementById('popupCode').classList.add('hidden');
    document.getElementById('popupCode').classList.remove('revealed');
    document.getElementById('revealBtn').style.display = 'inline-flex';
    document.getElementById('copyBtn').style.display = 'none';
    
    // Update vote counts
    updateVoteCounts(coupon);
    
    // Show popup
    document.getElementById('couponPopup').style.display = 'flex';
    setTimeout(() => {
        document.getElementById('couponPopup').classList.add('show');
    }, 10);
    
    // Track popup view
    trackCouponPopupView(coupon.id);
}

// Function to close coupon popup
function closeCouponPopup() {
    document.getElementById('couponPopup').classList.remove('show');
    setTimeout(() => {
        document.getElementById('couponPopup').style.display = 'none';
    }, 300);
    
    currentCoupon = null;
    couponRevealed = false;
}

// Function to reveal coupon code
function revealCouponCode() {
    if (!currentCoupon) return;
    
    const codeElement = document.getElementById('popupCode');
    const revealBtn = document.getElementById('revealBtn');
    const copyBtn = document.getElementById('copyBtn');
    
    // Reveal the code
    codeElement.classList.remove('hidden');
    codeElement.classList.add('revealed');
    
    // Hide reveal button and show copy button
    revealBtn.style.display = 'none';
    copyBtn.style.display = 'inline-flex';
    
    couponRevealed = true;
    
    // Track code reveal
    trackCouponCodeReveal(currentCoupon.id);
}

// Function to copy coupon code
function copyCouponCode() {
    if (!currentCoupon) return;
    
    navigator.clipboard.writeText(currentCoupon.code).then(() => {
        // Show success message
        const copyBtn = document.getElementById('copyBtn');
        const originalText = copyBtn.innerHTML;
        
        copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        copyBtn.classList.add('btn-success');
        
        setTimeout(() => {
            copyBtn.innerHTML = originalText;
            copyBtn.classList.remove('btn-success');
        }, 2000);
        
        // Track code copy
        trackCouponCodeCopy(currentCoupon.id);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        alert('Failed to copy coupon code');
    });
}

// Function to copy coupon link
function copyCouponLink() {
    const currentUrl = window.location.href;
    
    navigator.clipboard.writeText(currentUrl).then(() => {
        // Show success message
        const copyBtn = document.querySelector('.share-btn.copy');
        const originalIcon = copyBtn.innerHTML;
        
        copyBtn.innerHTML = '<i class="fas fa-check"></i>';
        copyBtn.style.color = '#28a745';
        
        setTimeout(() => {
            copyBtn.innerHTML = originalIcon;
            copyBtn.style.color = '#6c757d';
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        alert('Failed to copy link');
    });
}

// Function to vote on coupon
function voteCoupon(voteType) {
    if (!currentCoupon) return;
    
    // Send vote to server
    fetch('/api/coupons/' + currentCoupon.id + '/vote', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            vote_type: voteType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update vote counts
            updateVoteCounts(data.coupon);
            
            // Update button states
            updateVoteButtonStates(voteType, data.action);
            
            // Track vote
            trackCouponVote(currentCoupon.id, voteType, data.action);
        }
    })
    .catch(error => {
        console.error('Error voting:', error);
        alert('Failed to vote. Please try again.');
    });
}

// Function to update vote counts
function updateVoteCounts(coupon) {
    document.getElementById('upvoteCount').textContent = coupon.upvotes_count || 0;
    document.getElementById('downvoteCount').textContent = coupon.downvotes_count || 0;
    document.getElementById('likeCount').textContent = coupon.likes_count || 0;
    document.getElementById('dislikeCount').textContent = coupon.dislikes_count || 0;
}

// Function to update vote button states
function updateVoteButtonStates(voteType, action) {
    const buttons = ['upvoteBtn', 'downvoteBtn', 'likeBtn', 'dislikeBtn'];
    
    buttons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        btn.classList.remove('active', 'liked');
    });
    
    if (action === 'added') {
        const activeBtn = document.getElementById(voteType + 'Btn');
        if (voteType === 'like') {
            activeBtn.classList.add('liked');
        } else {
            activeBtn.classList.add('active');
        }
    }
}

// Function to share coupon
function shareCoupon(platform) {
    if (!currentCoupon) return;
    
    const shareData = {
        title: currentCoupon.title,
        text: `Check out this amazing coupon: ${currentCoupon.title}`,
        url: window.location.href
    };
    
    let shareUrl = '';
    
    switch (platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareData.url)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareData.text)}&url=${encodeURIComponent(shareData.url)}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${encodeURIComponent(shareData.text + ' ' + shareData.url)}`;
            break;
        case 'telegram':
            shareUrl = `https://t.me/share/url?url=${encodeURIComponent(shareData.url)}&text=${encodeURIComponent(shareData.text)}`;
            break;
        case 'email':
            shareUrl = `mailto:?subject=${encodeURIComponent(shareData.title)}&body=${encodeURIComponent(shareData.text + '\n\n' + shareData.url)}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
    
    // Track share
    trackCouponShare(currentCoupon.id, platform);
}

// Function to track coupon popup view
function trackCouponPopupView(couponId) {
    // Send analytics data
    if (typeof gtag !== 'undefined') {
        gtag('event', 'view_coupon_popup', {
            'coupon_id': couponId
        });
    }
}

// Function to track coupon code reveal
function trackCouponCodeReveal(couponId) {
    // Send analytics data
    if (typeof gtag !== 'undefined') {
        gtag('event', 'reveal_coupon_code', {
            'coupon_id': couponId
        });
    }
}

// Function to track coupon code copy
function trackCouponCodeCopy(couponId) {
    // Send analytics data
    if (typeof gtag !== 'undefined') {
        gtag('event', 'copy_coupon_code', {
            'coupon_id': couponId
        });
    }
}

// Function to track coupon click
function trackCouponClick() {
    if (!currentCoupon) return;
    
    // Send analytics data
    if (typeof gtag !== 'undefined') {
        gtag('event', 'click_coupon', {
            'coupon_id': currentCoupon.id,
            'coupon_title': currentCoupon.title,
            'store_name': currentCoupon.store_name
        });
    }
}

// Function to track coupon vote
function trackCouponVote(couponId, voteType, action) {
    // Send analytics data
    if (typeof gtag !== 'undefined') {
        gtag('event', 'vote_coupon', {
            'coupon_id': couponId,
            'vote_type': voteType,
            'action': action
        });
    }
}

// Function to track coupon share
function trackCouponShare(couponId, platform) {
    // Send analytics data
    if (typeof gtag !== 'undefined') {
        gtag('event', 'share_coupon', {
            'coupon_id': couponId,
            'platform': platform
        });
    }
}

// Close popup when clicking outside
document.addEventListener('click', function(event) {
    const popup = document.getElementById('couponPopup');
    if (event.target === popup) {
        closeCouponPopup();
    }
});

// Close popup with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCouponPopup();
    }
});
</script>