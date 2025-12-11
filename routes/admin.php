<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\TwoFactorController;

Route:: prefix('admin')->name('admin.')->group(function () {

    // ========================================
    // GUEST ROUTES (Setup & Login)
    // ========================================
    Route::middleware('guest: admin')->group(function () {
        Route::get('/setup', [AdminController::class, 'showSetup'])->name('setup. show');
        Route::post('/setup', [AdminController::class, 'storeSetup'])->name('setup.store');
        Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminController::class, 'login'])->name('login.attempt');
    });

    // ========================================
    // AUTHENTICATED ADMIN ROUTES
    // ========================================
    Route::middleware('auth:admin')->group(function () {
        
        // Dashboard
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Profile
        Route::get('/profile', [AdminController::class, 'editProfile'])->name('profile.edit');
        Route::patch('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

        // Users Management
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleUserActive'])->name('users.toggle');
        Route::patch('/users/{user}/email', [AdminController::class, 'updateUserEmail'])->name('users.email. update');

        // Logout
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

        // ========================================
        // 2FA ROUTES (INSIDE AUTH)
        // ========================================
        Route::get('/auth/2fa', [TwoFactorController::class, 'show'])->name('auth.twofactor.show');
        Route::post('/auth/2fa/verify', [TwoFactorController::class, 'verify'])->name('auth.twofactor. verify');
        Route::post('/auth/2fa/resend', [TwoFactorController::class, 'resend'])->name('auth.twofactor.resend');
    });
});