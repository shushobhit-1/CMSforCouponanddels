<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedInteger('discount_percentage')->nullable()->after('discount_text');
            $table->unsignedBigInteger('views_count')->default(0)->after('category_id');
            $table->unsignedBigInteger('clicks_count')->default(0)->after('views_count');
            $table->string('meta_title')->nullable()->after('clicks_count');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percentage', 'views_count', 'clicks_count', 'meta_title', 'meta_description', 'meta_keywords'
            ]);
        });
    }
};

