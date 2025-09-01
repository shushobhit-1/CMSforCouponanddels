<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;

class OptimizePerformance extends Command
{
    protected $signature = 'app:optimize-performance {--images} {--cache} {--all}';
    protected $description = 'Optimize application performance by caching data and optimizing images';

    public function handle()
    {
        $cacheService = new CacheService();

        if ($this->option('all') || $this->option('cache')) {
            $this->info('Preloading critical data...');
            $cacheService->preloadCriticalData();
            $this->info('✓ Critical data cached');
        }

        if ($this->option('all') || $this->option('images')) {
            $this->info('Optimizing images...');
            $optimized = $cacheService->optimizeImages();
            $this->info("✓ Optimized {$optimized} images");
        }

        if ($this->option('all')) {
            // Run additional optimizations
            $this->info('Running additional optimizations...');
            
            // Clear and rebuild route cache
            $this->call('route:cache');
            $this->info('✓ Route cache rebuilt');
            
            // Clear and rebuild config cache
            $this->call('config:cache');
            $this->info('✓ Config cache rebuilt');
            
            // Clear and rebuild view cache
            $this->call('view:cache');
            $this->info('✓ View cache rebuilt');
        }

        $this->info('🚀 Performance optimization completed!');
    }
}