<?php

use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\KostController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ── Root redirect ─────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('dashboard'));

// ── Guest routes ──────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',            [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',           [AuthController::class, 'login']);
    Route::get('/register',         [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',        [AuthController::class, 'register']);
    Route::get('/forgot-password',  [PasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',  [PasswordController::class, 'resetPassword'])->name('password.update');
});

// ── Authenticated routes ──────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',                [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',              [ProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [PasswordController::class, 'showChangeForm'])->name('password');
        Route::put('/change-password', [PasswordController::class, 'changePassword'])->name('password.update');
    });

    // Kost
    Route::get('/kost',  [KostController::class, 'index'])->name('kost.index');
    Route::post('/kost', [KostController::class, 'store'])->name('kost.store');

    // Rooms
    Route::resource('rooms', RoomController::class);

    // Tenants
    Route::resource('tenants', TenantController::class);

    // Invoices
    Route::resource('invoices', InvoiceController::class)->except(['edit','update']);
    Route::post('/invoices/generate-bulk', [InvoiceController::class, 'generateBulk'])->name('invoices.generate-bulk');

    // Payments
    Route::get('/payments',                        [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}',              [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/invoices/{invoice}/upload',       [PaymentController::class, 'upload'])->name('payments.upload');
    Route::post('/invoices/{invoice}/upload',      [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/payments/{payment}/verify',      [PaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/payments/{payment}/reject',      [PaymentController::class, 'reject'])->name('payments.reject');

    // Complaints
    Route::resource('complaints', ComplaintController::class)->except(['edit','update','destroy']);
    Route::post('/complaints/{complaint}/reply',         [ComplaintController::class, 'reply'])->name('complaints.reply');
    Route::patch('/complaints/{complaint}/update-status',[ComplaintController::class, 'updateStatus'])->name('complaints.update-status');

    // Reports
    Route::get('/reports',          [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf',      [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/excel',    [ReportController::class, 'exportExcel'])->name('reports.excel');

    // Settings
    Route::get('/settings',  [SettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings',[SettingController::class, 'update'])->name('settings.update');

    // Notifications
    Route::get('/notifications',                          [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/read',      [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read',           [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::get('/api/notifications/unread',               [NotificationController::class, 'getUnread'])->name('notifications.unread');
});
