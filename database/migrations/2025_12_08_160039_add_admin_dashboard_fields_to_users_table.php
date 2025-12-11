<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('password');
            }
            if (!Schema::hasColumn('users', 'plan')) {
                $table->string('plan')->default('free')->after('is_active'); // free|pro|business
            }
            if (!Schema::hasColumn('users', 'plan_expires_at')) {
                $table->timestamp('plan_expires_at')->nullable()->after('plan');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('last_login_at');
            }
            // Index for online tracking
            if (!collect(Schema::getColumnListing('users'))->contains('last_activity_at')) {
                $table->index('last_activity_at');
            }
        });
    }

    public function down(): void
    {
        // For safety in production, do not drop these columns automatically.
        // If you need to revert, write a manual migration to remove specific columns.
    }
};