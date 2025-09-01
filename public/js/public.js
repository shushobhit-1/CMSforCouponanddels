/**
 * Public Frontend JavaScript
 */

// Global variables
let isAuthenticated = false;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Main initialization function
function initializeApp() {
    setupCSRFToken();
    setupSearchFunctionality();
    setupFavoriteButtons();
    setupNewsletterForm();
    checkAuthStatus();
}

// Setup CSRF token for AJAX requests
function setupCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.csrfToken = token.getAttribute('content');
        window.fetchDefaults = {
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };
    }
}

// Check if user is authenticated
function checkAuthStatus() {
    isAuthenticated = document.body.dataset.authenticated === 'true';
}

// Setup search functionality
function setupSearchFunctionality() {
    const searchForms = document.querySelectorAll('.search-form');
    searchForms.forEach(form => {
        form.addEventListener('submit', handleSearch);
    });
}

// Handle search form submission
function handleSearch(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const query = formData.get('search') || formData.get('q');
    
    if (query && query.trim()) {
        window.location.href = `/search?q=${encodeURIComponent(query.trim())}`;
    }
}

// Setup favorite buttons
function setupFavoriteButtons() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.favorite-btn')) {
            e.preventDefault();
            handleFavoriteToggle(e.target.closest('.favorite-btn'));
        }
    });
}

// Handle favorite toggle
async function handleFavoriteToggle(button) {
    if (!isAuthenticated) {
        showLoginPrompt();
        return;
    }
    
    const type = button.dataset.type;
    const id = button.dataset.id;
    
    try {
        button.disabled = true;
        const response = await fetch('/api/toggle-favorite', {
            method: 'POST',
            ...window.fetchDefaults,
            body: JSON.stringify({ type, id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            updateFavoriteButton(button, result.favorited);
            showSuccess(result.message);
        } else {
            showError(result.message);
        }
    } catch (error) {
        showError('Failed to update favorite');
    } finally {
        button.disabled = false;
    }
}

// Update favorite button appearance
function updateFavoriteButton(button, isFavorited) {
    const icon = button.querySelector('i');
    if (isFavorited) {
        icon.classList.remove('far');
        icon.classList.add('fas', 'text-danger');
    } else {
        icon.classList.remove('fas', 'text-danger');
        icon.classList.add('far');
    }
}

// Show coupon popup
async function showCouponPopup(couponId, couponTitle = 'Coupon') {
    try {
        const response = await fetch(`/api/track-coupon-click`, {
            method: 'POST',
            ...window.fetchDefaults,
            body: JSON.stringify({ coupon_id: couponId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                title: 'Coupon Code Revealed!',
                html: `
                    <div class="coupon-popup-content">
                        <div class="coupon-code-display p-3 bg-primary text-white rounded mb-3" style="cursor: pointer;" onclick="copyToClipboard('${result.code}')">
                            <h3 class="mb-0">${result.code}</h3>
                            <small>Click to copy</small>
                        </div>
                        <p class="mb-3">Use this code at checkout!</p>
                        <div class="share-buttons">
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="shareOnFacebook('${couponTitle}', '${result.code}')">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info me-2" onclick="shareOnTwitter('${couponTitle}', '${result.code}')">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="shareOnWhatsApp('${couponTitle}', '${result.code}')">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Go to Store',
                cancelButtonText: 'Close'
            }).then((result) => {
                if (result.isConfirmed && response.affiliate_url) {
                    window.open(response.affiliate_url, '_blank');
                }
            });
        }
    } catch (error) {
        showError('Failed to load coupon');
    }
}

// Social sharing functions
function shareOnFacebook(title, text) {
    const url = encodeURIComponent(window.location.href);
    const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function shareOnTwitter(title, text) {
    const url = encodeURIComponent(window.location.href);
    const shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${url}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function shareOnWhatsApp(title, text) {
    const shareText = encodeURIComponent(`${title}\n${window.location.href}`);
    const shareUrl = `https://wa.me/?text=${shareText}`;
    window.open(shareUrl, '_blank');
}

// Copy to clipboard
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showSuccess('Copied to clipboard!');
    } catch (error) {
        showSuccess('Code copied!');
    }
}

// Newsletter subscription
function setupNewsletterForm() {
    const forms = document.querySelectorAll('.newsletter-form');
    forms.forEach(form => {
        form.addEventListener('submit', handleNewsletterSubmission);
    });
}

async function handleNewsletterSubmission(e) {
    e.preventDefault();
    const email = e.target.querySelector('input[type="email"]').value;
    
    if (email) {
        showSuccess('Successfully subscribed to newsletter!');
        e.target.reset();
    }
}

// Utility functions
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: message,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: message,
        timer: 5000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function showLoginPrompt() {
    Swal.fire({
        title: 'Login Required',
        text: 'Please login to use this feature',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Login',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/login';
        }
    });
}

// Make functions globally available
window.showCouponPopup = showCouponPopup;
window.shareOnFacebook = shareOnFacebook;
window.shareOnTwitter = shareOnTwitter;
window.shareOnWhatsApp = shareOnWhatsApp;
window.copyToClipboard = copyToClipboard;