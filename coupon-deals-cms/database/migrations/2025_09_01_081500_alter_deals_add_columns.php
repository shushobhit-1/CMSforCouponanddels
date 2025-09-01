<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('slug')->unique()->after('title');
            $table->text('description')->nullable()->after('slug');
            $table->decimal('original_price', 10, 2)->nullable()->after('description');
            $table->decimal('deal_price', 10, 2)->nullable()->after('original_price');
            $table->unsignedInteger('discount_percentage')->nullable()->after('deal_price');
            $table->timestamp('starts_at')->nullable()->after('discount_percentage');
            $table->timestamp('expires_at')->nullable()->after('starts_at');
            $table->boolean('is_featured')->default(false)->after('expires_at');
            $table->boolean('is_active')->default(true)->after('is_featured');
            $table->string('affiliate_url')->nullable()->after('is_active');
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete()->after('affiliate_url');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->after('store_id');
            $table->string('meta_title')->nullable()->after('category_id');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->unsignedBigInteger('views_count')->default(0)->after('meta_keywords');
            $table->unsignedBigInteger('clicks_count')->default(0)->after('views_count');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'slug', 'description', 'original_price', 'deal_price', 'discount_percentage',
                'starts_at', 'expires_at', 'is_featured', 'is_active', 'affiliate_url', 'meta_title',
                'meta_description', 'meta_keywords', 'views_count', 'clicks_count'
            ]);
            $table->dropConstrainedForeignId('store_id');
            $table->dropConstrainedForeignId('category_id');
            $table->dropSoftDeletes();
        });
    }
};

