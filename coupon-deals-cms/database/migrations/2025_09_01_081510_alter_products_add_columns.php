<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('slug')->unique()->after('title');
            $table->text('description')->nullable()->after('slug');
            $table->text('short_description')->nullable()->after('description');
            $table->decimal('price', 10, 2)->nullable()->after('short_description');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->unsignedInteger('discount_percentage')->nullable()->after('discount_price');
            $table->boolean('is_featured')->default(false)->after('discount_percentage');
            $table->boolean('is_active')->default(true)->after('is_featured');
            $table->string('affiliate_url')->nullable()->after('is_active');
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete()->after('affiliate_url');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->after('store_id');
            $table->string('brand')->nullable()->after('category_id');
            $table->string('model')->nullable()->after('brand');
            $table->json('specifications')->nullable()->after('model');
            $table->string('meta_title')->nullable()->after('specifications');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->decimal('rating', 3, 1)->nullable()->after('meta_keywords');
            $table->unsignedInteger('review_count')->default(0)->after('rating');
            $table->unsignedBigInteger('views_count')->default(0)->after('review_count');
            $table->unsignedBigInteger('clicks_count')->default(0)->after('views_count');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'slug', 'description', 'short_description', 'price', 'discount_price',
                'discount_percentage', 'is_featured', 'is_active', 'affiliate_url', 'brand', 'model',
                'specifications', 'meta_title', 'meta_description', 'meta_keywords', 'rating',
                'review_count', 'views_count', 'clicks_count'
            ]);
            $table->dropConstrainedForeignId('store_id');
            $table->dropConstrainedForeignId('category_id');
            $table->dropSoftDeletes();
        });
    }
};

