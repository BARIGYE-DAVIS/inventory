<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('business_category_id'); // MUST BE UNSIGNED BIGINT
            $table->string('email')->unique();
            $table->string('phone', 20);
            $table->text('address')->nullable();
            $table->string('tax_number', 50)->nullable()->comment('URA TIN');
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('subscription_plan', ['trial', 'basic', 'standard', 'premium'])->default('trial');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Add foreign key AFTER column creation
            $table->foreign('business_category_id')
                  ->references('id')
                  ->on('business_categories')
                  ->onDelete('restrict');

            // Add indexes
            $table->index('slug');
            $table->index('email');
            $table->index('is_active');
            $table->index('business_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};