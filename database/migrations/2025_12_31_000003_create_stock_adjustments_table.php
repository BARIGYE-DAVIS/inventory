<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('stock_taking_session_id')->nullable();
            $table->timestamp('adjustment_date');
            $table->decimal('physical_count', 15, 2)->nullable()->comment('Actual physical count');
            $table->decimal('system_quantity', 15, 2)->comment('System recorded quantity');
            $table->decimal('variance', 15, 2)->comment('Difference: physical - system');
            $table->decimal('adjustment_quantity', 15, 2)->comment('How much to adjust');
            $table->enum('reason', [
                'Stock Take',
                'Spoilage',
                'Damage',
                'Theft',
                'Data Entry Error',
                'Missing',
                'Overstock',
                'Other'
            ])->default('Stock Take');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'applied'])->default('pending');
            $table->unsignedBigInteger('recorded_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->foreign('stock_taking_session_id')->references('id')->on('stock_taking_sessions')->onDelete('set null');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('restrict');
            
            $table->index('business_id');
            $table->index('product_id');
            $table->index('adjustment_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
