<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add opening_stock to products table
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('opening_stock', 15, 2)->default(0)->after('quantity')->comment('Original opening stock - never changes');
            $table->timestamp('last_period_closed_date')->nullable()->after('opening_stock')->comment('When was the last inventory period closed');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['opening_stock', 'last_period_closed_date']);
        });
    }
};
