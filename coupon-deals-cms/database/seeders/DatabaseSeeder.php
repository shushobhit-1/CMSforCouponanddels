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
    }
}
