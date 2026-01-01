<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_taking_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->timestamp('session_date');
            $table->string('notes')->nullable();
            $table->enum('status', ['active', 'submitted', 'closed'])->default('active');
            $table->unsignedBigInteger('initiated_by');
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('initiated_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('restrict');
            $table->index('business_id');
            $table->index('session_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_taking_sessions');
    }
};
