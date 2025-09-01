<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    Admin\DashboardController,
    Admin\CouponController,
    Admin\DealController,
    Admin\ProductController,
    Admin\StoreController,
    Admin\CategoryController,
    Admin\SettingController,
    Admin\UserController,
    Admin\ThemeController,
    Admin\MenuController,
    Admin\SliderController,
    Admin\AffiliateController,
    User\ProfileController,
    User\FavoriteController,
    User\NotificationController,
    Public\CouponPublicController,
    Public\DealPublicController,
    Public\ProductPublicController,
    Public\StorePublicController,
    Api\ApiController
};

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/coupons', [CouponPublicController::class, 'index'])->name('coupons.index');
Route::get('/coupons/{coupon:slug}', [CouponPublicController::class, 'show'])->name('coupons.show');
Route::get('/deals', [DealPublicController::class, 'index'])->name('deals.index');
Route::get('/deals/{deal:slug}', [DealPublicController::class, 'show'])->name('deals.show');
Route::get('/products', [ProductPublicController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductPublicController::class, 'show'])->name('products.show');
Route::get('/stores', [StorePublicController::class, 'index'])->name('stores.index');
Route::get('/stores/{store:slug}', [StorePublicController::class, 'show'])->name('stores.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// API Routes
Route::prefix('api')->group(function () {
    Route::post('/track-coupon-click', [ApiController::class, 'trackCouponClick']);
    Route::post('/track-affiliate-click', [ApiController::class, 'trackAffiliateClick']);
    Route::post('/toggle-favorite', [ApiController::class, 'toggleFavorite'])->middleware('auth');
    Route::get('/search-suggestions', [ApiController::class, 'searchSuggestions']);
    Route::post('/newsletter-subscribe', [ApiController::class, 'subscribeNewsletter']);
    Route::get('/popular-searches', [ApiController::class, 'popularSearches']);
});

// Coupon reveal route
Route::post('/coupons/{coupon}/reveal', [CouponPublicController::class, 'reveal'])->name('coupons.reveal');

// Deal tracking route
Route::get('/deals/{deal}/track', [DealPublicController::class, 'track'])->name('deals.track');

// Product tracking route
Route::get('/products/{product}/track', [ProductPublicController::class, 'track'])->name('products.track');

// User Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
        Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    });
});

// Admin Routes (require admin authentication)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Coupons Management
    Route::resource('coupons', CouponController::class);
    Route::post('coupons/bulk-action', [CouponController::class, 'bulkAction'])->name('coupons.bulk-action');
    
    // Deals Management
    Route::resource('deals', DealController::class);
    Route::post('deals/bulk-action', [DealController::class, 'bulkAction'])->name('deals.bulk-action');
    
    // Products Management
    Route::resource('products', ProductController::class);
    Route::post('products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');
    
    // Stores Management
    Route::resource('stores', StoreController::class);
    
    // Categories Management
    Route::resource('categories', CategoryController::class);
    
    // Users Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('users.toggle-ban');
    Route::post('users/{user}/verify-email', [UserController::class, 'verifyEmail'])->name('users.verify-email');
    
    // Settings Management
    Route::get('settings/general', [SettingController::class, 'general'])->name('settings.general');
    Route::post('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general.update');
    Route::get('settings/seo', [SettingController::class, 'seo'])->name('settings.seo');
    Route::post('settings/seo', [SettingController::class, 'updateSeo'])->name('settings.seo.update');
    Route::get('settings/integrations', [SettingController::class, 'integrations'])->name('settings.integrations');
    Route::post('settings/integrations', [SettingController::class, 'updateIntegrations'])->name('settings.integrations.update');
    
    // Theme Management
    Route::get('theme', [ThemeController::class, 'edit'])->name('theme.edit');
    Route::post('theme', [ThemeController::class, 'update'])->name('theme.update');
    Route::post('theme/reset', [ThemeController::class, 'reset'])->name('theme.reset');
    
    // Menu Management
    Route::resource('menus', MenuController::class);
    Route::post('menus/{menu}/add-item', [MenuController::class, 'addItem'])->name('menus.add-item');
    Route::post('menus/{menu}/update-order', [MenuController::class, 'updateOrder'])->name('menus.update-order');
    
    // Slider Management
    Route::resource('sliders', SliderController::class);
    Route::post('sliders/update-order', [SliderController::class, 'updateOrder'])->name('sliders.update-order');
    Route::post('sliders/{slider}/toggle-status', [SliderController::class, 'toggleStatus'])->name('sliders.toggle-status');
    
    // Affiliate Management
    Route::resource('affiliates', AffiliateController::class);
    Route::post('affiliates/{affiliate}/test-connection', [AffiliateController::class, 'testConnection'])->name('affiliates.test-connection');
    Route::post('affiliates/{affiliate}/sync', [AffiliateController::class, 'sync'])->name('affiliates.sync');
});

// Authentication Routes
require __DIR__.'/auth.php';