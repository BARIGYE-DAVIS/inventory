<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('sku', 100);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit', 50)->default('pcs')->comment('pcs, kg, liters, boxes, etc.');
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->integer('reorder_level')->default(0);
            $table->string('barcode', 100)->nullable();
            $table->string('image')->nullable();
            
            // ✅ NEW: Expiry date fields
            $table->date('manufacture_date')->nullable()->comment('Manufacturing date');
            $table->date('expiry_date')->nullable()->comment('Expiration date');
            $table->boolean('track_expiry')->default(false)->comment('Enable expiry tracking for this product');
            $table->integer('expiry_alert_days')->default(30)->comment('Alert X days before expiry');
            
            $table->boolean('has_variants')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')
                  ->references('id')
                  ->on('businesses')
                  ->onDelete('cascade');

            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');

            $table->unique(['business_id', 'sku']);
            $table->index('business_id');
            $table->index('category_id');
            $table->index('expiry_date'); // ✅ Index for quick expiry queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};