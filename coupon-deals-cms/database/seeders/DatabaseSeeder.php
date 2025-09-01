<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Menu;
use App\Models\Setting;
use App\Models\Slider;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Optionally create permissions
        $manageEverything = Permission::firstOrCreate(['name' => 'manage everything']);
        $adminRole->givePermissionTo($manageEverything);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // Sample data
        // Ensure base sets exist
        if (Store::count() === 0) {
            Store::factory(12)->create();
        }

        if (Category::count() === 0) {
            Category::factory(10)->create();
        }

        // Coupons
        if (Coupon::count() === 0) {
            Coupon::factory(50)->create();
        }

        // Deals
        if (Deal::count() === 0) {
            Deal::factory(24)->create();
        }

        // Products
        if (Product::count() === 0) {
            Product::factory(40)->create();
        }

        // Default menus
        Menu::firstOrCreate(
            ['location' => 'primary'],
            [
                'name' => 'Primary Menu',
                'items' => [
                    ['label' => 'Home', 'url' => route('home'), 'target' => '_self'],
                    ['label' => 'Coupons', 'url' => route('coupons.index'), 'target' => '_self'],
                    ['label' => 'Deals', 'url' => route('deals.index'), 'target' => '_self'],
                    ['label' => 'Products', 'url' => route('products.index'), 'target' => '_self'],
                    ['label' => 'Stores', 'url' => route('stores.index'), 'target' => '_self'],
                ],
                'is_active' => true,
            ]
        );

        // Theme settings
        Setting::updateOrCreate(
            ['key' => 'theme'],
            ['value' => [
                'primary_color' => '#007bff',
                'secondary_color' => '#6c757d',
                'font_family' => 'Inter, sans-serif',
                'rounded' => true,
            ], 'group' => 'appearance']
        );

        // Header/Footer editor content
        Setting::updateOrCreate(
            ['key' => 'header_html'],
            ['value' => ['html' => '<!-- custom header html -->'], 'group' => 'appearance']
        );
        Setting::updateOrCreate(
            ['key' => 'footer_html'],
            ['value' => ['html' => '<p class="mb-0">&copy; '.date('Y').' CouponDeals</p>'], 'group' => 'appearance']
        );

        // Default slider
        Slider::firstOrCreate(
            ['slug' => 'home-hero'],
            [
                'title' => 'Homepage Hero',
                'slides' => [
                    ['title' => 'Big Savings', 'subtitle' => 'Up to 70% off', 'image' => '/images/slider/slide1.jpg', 'cta_label' => 'Browse Coupons', 'cta_url' => route('coupons.index')],
                    ['title' => 'Hot Deals', 'subtitle' => 'Limited time offers', 'image' => '/images/slider/slide2.jpg', 'cta_label' => 'See Deals', 'cta_url' => route('deals.index')],
                ],
                'is_active' => true,
            ]
        );
    }
}
