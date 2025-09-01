<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\CouponPublicController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DealPublicController;
use App\Http\Controllers\ProductPublicController;
use App\Http\Controllers\StorePublicController;
use App\Http\Controllers\CategoryPublicController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Admin\ThemeController as AdminThemeController;
use App\Http\Controllers\Admin\AppearanceController as AdminAppearanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public Routes
Route::get('/coupons', [CouponPublicController::class, 'index'])->name('coupons.index');
Route::get('/coupons/{coupon:slug}', [CouponPublicController::class, 'show'])->name('coupons.show');

Route::get('/deals', [DealPublicController::class, 'index'])->name('deals.index');
Route::get('/deals/{deal:slug}', [DealPublicController::class, 'show'])->name('deals.show');

Route::get('/products', [ProductPublicController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductPublicController::class, 'show'])->name('products.show');

Route::get('/stores', [StorePublicController::class, 'index'])->name('stores.index');
Route::get('/stores/{store:slug}', [StorePublicController::class, 'show'])->name('stores.show');

Route::get('/categories', [CategoryPublicController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryPublicController::class, 'show'])->name('categories.show');

// API Routes for tracking
Route::prefix('api')->group(function () {
    Route::post('/track-coupon-click', [ApiController::class, 'trackCouponClick']);
    Route::post('/track-affiliate-click', [ApiController::class, 'trackAffiliateClick']);
    Route::post('/favorites/toggle', [ApiController::class, 'toggleFavorite'])->middleware('auth');
});

// Auth protected dashboard (user)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Admin area
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('sliders', AdminSliderController::class);
        Route::get('appearance/theme', [AdminThemeController::class, 'edit'])->name('appearance.theme.edit');
        Route::put('appearance/theme', [AdminThemeController::class, 'update'])->name('appearance.theme.update');
        Route::get('appearance/header', [AdminAppearanceController::class, 'header'])->name('appearance.header.edit');
        Route::post('appearance/header', [AdminAppearanceController::class, 'saveHeader'])->name('appearance.header.save');
        Route::get('appearance/footer', [AdminAppearanceController::class, 'footer'])->name('appearance.footer.edit');
        Route::post('appearance/footer', [AdminAppearanceController::class, 'saveFooter'])->name('appearance.footer.save');
        Route::get('appearance/menus', [AdminAppearanceController::class, 'menus'])->name('appearance.menus.edit');
        Route::post('appearance/menus', [AdminAppearanceController::class, 'saveMenus'])->name('appearance.menus.save');
    });

require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
