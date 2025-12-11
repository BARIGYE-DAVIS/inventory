<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_categories', function (Blueprint $table) {
            $table->id(); // This creates BIGINT UNSIGNED
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_categories');
    }
};