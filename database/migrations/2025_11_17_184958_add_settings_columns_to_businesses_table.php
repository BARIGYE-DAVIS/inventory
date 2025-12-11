<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // Logo
            
            
            // Email/SMTP
            $table->string('smtp_email')->nullable()->after('email');
            $table->string('smtp_password')->nullable()->after('smtp_email');
            $table->boolean('email_configured')->default(false)->after('smtp_password');
            
            // Additional info
            $table->string('website')->nullable()->after('email_configured');
            
            // Tax settings
            $table->boolean('tax_enabled')->default(false)->after('tax_number');
            $table->decimal('tax_rate', 5, 2)->default(18.00)->after('tax_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                
                'smtp_email',
                'smtp_password',
                'email_configured',
                'website',
                'tax_enabled',
                'tax_rate',
            ]);
        });
    }
};