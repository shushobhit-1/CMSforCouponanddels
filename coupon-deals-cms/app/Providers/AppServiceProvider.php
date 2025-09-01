<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure SQLite enforces foreign keys during migrations and runtime
        try {
            if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
                \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys=ON');
            }
        } catch (\Throwable $e) {
            // ignore in non-DB contexts
        }
    }
}
