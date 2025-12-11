<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            // Status and roles
            $table->boolean('is_active')->default(true);
            $table->boolean('is_superadmin')->default(false);

            // Two-factor (email-based OTP, reused from existing flow)
            $table->boolean('two_factor_enabled')->default(true);
            $table->string('two_factor_code')->nullable();
            $table->timestamp('two_factor_expires_at')->nullable();

            // Activity tracking
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();

            // Session
            $table->rememberToken();

            $table->timestamps();

            // Helpful indexes
            $table->index('is_active');
            $table->index('last_activity_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};