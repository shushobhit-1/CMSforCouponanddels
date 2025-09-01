/**
 * Service Worker for CouponDeals PWA
 * Handles caching, offline functionality, and push notifications
 */

const CACHE_NAME = 'coupondeals-v1.0.0';
const STATIC_CACHE = 'static-v1.0.0';
const DYNAMIC_CACHE = 'dynamic-v1.0.0';

// Files to cache immediately
const STATIC_FILES = [
    '/',
    '/coupons',
    '/deals',
    '/stores',
    '/css/public.css',
    '/js/public.js',
    '/manifest.json',
    '/images/logo.png',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
    'https://code.jquery.com/jquery-3.7.1.min.js',
    'https://cdn.jsdelivr.net/npm/sweetalert2@11'
];

// Install event - cache static files
self.addEventListener('install', event => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('Service Worker: Caching static files');
                return cache.addAll(STATIC_FILES);
            })
            .catch(error => {
                console.error('Service Worker: Failed to cache static files', error);
            })
    );
    
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker: Activating...');
    
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                        console.log('Service Worker: Deleting old cache', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    
    self.clients.claim();
});

// Fetch event - serve cached files or fetch from network
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip Chrome extension requests
    if (url.protocol === 'chrome-extension:') {
        return;
    }
    
    // Handle different types of requests
    if (isStaticAsset(request.url)) {
        event.respondWith(cacheFirst(request));
    } else if (isAPIRequest(request.url)) {
        event.respondWith(networkFirst(request));
    } else if (isPageRequest(request)) {
        event.respondWith(staleWhileRevalidate(request));
    } else {
        event.respondWith(networkFirst(request));
    }
});

// Push notification event
self.addEventListener('push', event => {
    console.log('Service Worker: Push notification received');
    
    const options = {
        body: 'New deals and coupons available!',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/icon-72x72.png',
        vibrate: [200, 100, 200],
        data: {
            url: '/'
        },
        actions: [
            {
                action: 'view',
                title: 'View Deals',
                icon: '/images/icons/view-icon.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/images/icons/close-icon.png'
            }
        ]
    };
    
    if (event.data) {
        try {
            const data = event.data.json();
            options.title = data.title || 'CouponDeals';
            options.body = data.body || options.body;
            options.icon = data.icon || options.icon;
            options.data.url = data.url || options.data.url;
        } catch (error) {
            console.error('Service Worker: Error parsing push data', error);
        }
    }
    
    event.waitUntil(
        self.registration.showNotification('CouponDeals', options)
    );
});

// Notification click event
self.addEventListener('notificationclick', event => {
    console.log('Service Worker: Notification clicked');
    
    event.notification.close();
    
    const action = event.action;
    const url = event.notification.data.url || '/';
    
    if (action === 'close') {
        return;
    }
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                // Check if the app is already open
                for (const client of clientList) {
                    if (client.url.includes(url) && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // Open new window if not already open
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});

// Background sync event
self.addEventListener('sync', event => {
    console.log('Service Worker: Background sync triggered', event.tag);
    
    if (event.tag === 'sync-favorites') {
        event.waitUntil(syncFavorites());
    } else if (event.tag === 'sync-analytics') {
        event.waitUntil(syncAnalytics());
    }
});

// Message event - communicate with main thread
self.addEventListener('message', event => {
    console.log('Service Worker: Message received', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Cache strategies
async function cacheFirst(request) {
    try {
        const cachedResponse = await caches.match(request);
        return cachedResponse || fetch(request);
    } catch (error) {
        console.error('Service Worker: Cache first strategy failed', error);
        return new Response('Offline', { status: 503 });
    }
}

async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok && shouldCache(request.url)) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('Service Worker: Network failed, trying cache', error);
        const cachedResponse = await caches.match(request);
        return cachedResponse || createOfflineResponse();
    }
}

async function staleWhileRevalidate(request) {
    const cachedResponse = caches.match(request);
    
    const fetchPromise = fetch(request).then(networkResponse => {
        if (networkResponse.ok) {
            const cache = caches.open(DYNAMIC_CACHE);
            cache.then(c => c.put(request, networkResponse.clone()));
        }
        return networkResponse;
    }).catch(() => {
        console.log('Service Worker: Network failed for', request.url);
    });
    
    return (await cachedResponse) || (await fetchPromise) || createOfflineResponse();
}

// Helper functions
function isStaticAsset(url) {
    return url.includes('.css') || 
           url.includes('.js') || 
           url.includes('.png') || 
           url.includes('.jpg') || 
           url.includes('.svg') || 
           url.includes('.woff') || 
           url.includes('.woff2');
}

function isAPIRequest(url) {
    return url.includes('/api/');
}

function isPageRequest(request) {
    return request.mode === 'navigate' || 
           (request.method === 'GET' && request.headers.get('accept').includes('text/html'));
}

function shouldCache(url) {
    return !url.includes('/api/track-') && 
           !url.includes('/api/analytics') && 
           !url.includes('chrome-extension');
}

function createOfflineResponse() {
    return new Response(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Offline - CouponDeals</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    text-align: center; 
                    padding: 50px; 
                    background: #f5f5f5; 
                }
                .offline-container { 
                    max-width: 400px; 
                    margin: 0 auto; 
                    background: white; 
                    padding: 40px; 
                    border-radius: 10px; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
                }
                .offline-icon { 
                    font-size: 4rem; 
                    color: #007bff; 
                    margin-bottom: 20px; 
                }
                h1 { 
                    color: #333; 
                    margin-bottom: 10px; 
                }
                p { 
                    color: #666; 
                    margin-bottom: 20px; 
                }
                .retry-btn { 
                    background: #007bff; 
                    color: white; 
                    border: none; 
                    padding: 10px 20px; 
                    border-radius: 5px; 
                    cursor: pointer; 
                }
            </style>
        </head>
        <body>
            <div class="offline-container">
                <div class="offline-icon">📱</div>
                <h1>You're Offline</h1>
                <p>Please check your internet connection and try again.</p>
                <button class="retry-btn" onclick="window.location.reload()">
                    Retry
                </button>
            </div>
        </body>
        </html>
    `, {
        status: 503,
        headers: { 'Content-Type': 'text/html' }
    });
}

// Sync functions
async function syncFavorites() {
    try {
        // Get stored favorites from IndexedDB or localStorage
        const storedFavorites = await getStoredFavorites();
        
        for (const favorite of storedFavorites) {
            await fetch('/api/toggle-favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': await getCSRFToken()
                },
                body: JSON.stringify(favorite)
            });
        }
        
        // Clear stored favorites after sync
        await clearStoredFavorites();
        console.log('Service Worker: Favorites synced successfully');
    } catch (error) {
        console.error('Service Worker: Failed to sync favorites', error);
    }
}

async function syncAnalytics() {
    try {
        // Sync any pending analytics data
        const storedAnalytics = await getStoredAnalytics();
        
        for (const analytics of storedAnalytics) {
            await fetch('/api/analytics', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': await getCSRFToken()
                },
                body: JSON.stringify(analytics)
            });
        }
        
        await clearStoredAnalytics();
        console.log('Service Worker: Analytics synced successfully');
    } catch (error) {
        console.error('Service Worker: Failed to sync analytics', error);
    }
}

// Storage helpers (would need IndexedDB implementation)
async function getStoredFavorites() {
    // Implement IndexedDB storage
    return [];
}

async function clearStoredFavorites() {
    // Implement IndexedDB clearing
}

async function getStoredAnalytics() {
    // Implement IndexedDB storage
    return [];
}

async function clearStoredAnalytics() {
    // Implement IndexedDB clearing
}

async function getCSRFToken() {
    // Get CSRF token from cached page or API
    try {
        const response = await fetch('/csrf-token');
        const data = await response.json();
        return data.token;
    } catch {
        return null;
    }
}

console.log('Service Worker: Loaded successfully');