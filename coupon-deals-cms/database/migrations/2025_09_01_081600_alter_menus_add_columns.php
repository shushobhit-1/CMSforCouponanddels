<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('location')->nullable()->after('name');
            $table->json('items')->nullable()->after('location');
            $table->boolean('is_active')->default(true)->after('items');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['name','location','items','is_active']);
        });
    }
};

