<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('location_id');
            $table->decimal('quantity', 15, 2)->default(0);
            $table->timestamp('updated_at');

            $table->foreign('business_id')
                  ->references('id')
                  ->on('businesses')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');

            $table->foreign('location_id')
                  ->references('id')
                  ->on('locations')
                  ->onDelete('cascade');

            $table->unique(['product_id', 'location_id']);
            $table->index('business_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};