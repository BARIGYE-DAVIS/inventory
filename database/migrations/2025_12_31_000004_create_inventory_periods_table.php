<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('product_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('opening_stock', 15, 2)->comment('Opening stock for this period');
            $table->decimal('purchases', 15, 2)->default(0)->comment('Total purchases in period');
            $table->decimal('sales', 15, 2)->default(0)->comment('Total sales in period');
            $table->decimal('adjustments', 15, 2)->default(0)->comment('Total adjustments in period');
            $table->decimal('calculated_stock', 15, 2)->comment('Opening + Purchases - Sales + Adjustments');
            $table->decimal('physical_count', 15, 2)->nullable()->comment('Physical count at end of period');
            $table->decimal('closing_stock', 15, 2)->comment('Final closing stock for period');
            $table->decimal('variance', 15, 2)->default(0)->comment('Difference between physical and calculated');
            $table->decimal('variance_percentage', 8, 2)->default(0)->comment('Variance as percentage');
            $table->enum('status', ['open', 'closed', 'locked'])->default('open')->comment('open=still recording, closed=ready for review, locked=finalized');
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('restrict');
            
            $table->unique(['product_id', 'period_start'], 'unique_product_period');
            $table->index('business_id');
            $table->index(['period_start', 'period_end']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_periods');
    }
};
