<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupon_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->enum('vote_type', ['upvote', 'downvote', 'like', 'dislike']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Prevent duplicate votes from same user/IP for same coupon
            $table->unique(['coupon_id', 'user_id', 'vote_type'], 'unique_user_vote');
            $table->unique(['coupon_id', 'ip_address', 'vote_type'], 'unique_ip_vote');
            
            // Indexes for performance
            $table->index(['coupon_id', 'vote_type']);
            $table->index(['user_id', 'vote_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_votes');
    }
};