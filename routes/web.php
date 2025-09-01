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
    Public\StorePublicController
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
Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category.show');

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
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
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
    Route::post('stores/bulk-action', [StoreController::class, 'bulkAction'])->name('stores.bulk-action');
    
    // Categories Management
    Route::resource('categories', CategoryController::class);
    Route::post('categories/bulk-action', [CategoryController::class, 'bulkAction'])->name('categories.bulk-action');
    
    // Users Management
    Route::resource('users', UserController::class);
    Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
    
    // Settings Management
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    
    // Theme Management
    Route::get('theme', [ThemeController::class, 'index'])->name('theme.index');
    Route::put('theme', [ThemeController::class, 'update'])->name('theme.update');
    
    // Menu Management
    Route::resource('menus', MenuController::class);
    Route::post('menus/update-order', [MenuController::class, 'updateOrder'])->name('menus.update-order');
    
    // Slider Management
    Route::resource('sliders', SliderController::class);
    Route::post('sliders/update-order', [SliderController::class, 'updateOrder'])->name('sliders.update-order');
    
    // Affiliate Management
    Route::get('affiliates', [AffiliateController::class, 'index'])->name('affiliates.index');
    Route::put('affiliates', [AffiliateController::class, 'update'])->name('affiliates.update');
});

// Authentication Routes
require __DIR__.'/auth.php';