<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('purchase_number', 100)->unique();
            $table->timestamp('purchase_date');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')
                  ->references('id')
                  ->on('businesses')
                  ->onDelete('cascade');

            $table->foreign('location_id')
                  ->references('id')
                  ->on('locations')
                  ->onDelete('restrict');

            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers')
                  ->onDelete('set null');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');

            $table->index('business_id');
            $table->index('purchase_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};