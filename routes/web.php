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
use App\Http\Controllers\Admin\RentalApplicationController as AdminRentalApplicationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Public\KostController as PublicKostController;
use App\Http\Controllers\Tenant\ComplaintController as TenantComplaintController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\InvoiceController as TenantInvoiceController;
use App\Http\Controllers\Tenant\PaymentController as TenantPaymentController;
use App\Http\Controllers\Tenant\RentalApplicationController as TenantRentalApplicationController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════════
//  PUBLIC — Tidak perlu login
// ══════════════════════════════════════════════════════════════════
Route::get('/', fn() => redirect()->route('public.kosts.index'))->name('home');

Route::get('/kos',      [PublicKostController::class, 'index'])->name('public.kosts.index');
Route::get('/kos/{kost}', [PublicKostController::class, 'show'])->name('public.kosts.show');

// ══════════════════════════════════════════════════════════════════
//  GUEST — Hanya bisa diakses jika BELUM login
// ══════════════════════════════════════════════════════════════════
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

// ══════════════════════════════════════════════════════════════════
//  AUTHENTICATED — Bisa diakses oleh semua user yang sudah login
// ══════════════════════════════════════════════════════════════════
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile (bisa admin maupun tenant)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',                [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',              [ProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [PasswordController::class, 'showChangeForm'])->name('password');
        Route::put('/change-password', [PasswordController::class, 'changePassword'])->name('password.update');
    });

    // Notifikasi (semua user)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',                                    [NotificationController::class, 'index'])->name('index');
        Route::get('/{notification}/read',                 [NotificationController::class, 'markRead'])->name('read');
        Route::post('/mark-all-read',                      [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::get('/api/unread',                          [NotificationController::class, 'getUnread'])->name('unread');
    });
});

// ══════════════════════════════════════════════════════════════════
//  ADMIN — Hanya untuk role admin
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // ── Onboarding (setup kos pertama kali — tanpa middleware onboarding) ──
    Route::get('/kost/setup',   [KostController::class, 'setup'])->name('kost.setup');
    Route::post('/kost/setup',  [KostController::class, 'doSetup'])->name('kost.setup.store');

    // ── Routes yang butuh kos sudah ada ──
    Route::middleware('onboarding')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Kost settings
        Route::get('/kost',  [KostController::class, 'index'])->name('kost.index');
        Route::post('/kost', [KostController::class, 'store'])->name('kost.store');

        // Rooms
        Route::resource('rooms', RoomController::class);

        // Tenants
        Route::resource('tenants', TenantController::class);

        // Rental Applications
        Route::get('/applications',               [AdminRentalApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [AdminRentalApplicationController::class, 'show'])->name('applications.show');
        Route::post('/applications/{application}/approve', [AdminRentalApplicationController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{application}/reject',  [AdminRentalApplicationController::class, 'reject'])->name('applications.reject');

        // Invoices
        Route::resource('invoices', InvoiceController::class)->except(['edit', 'update']);
        Route::post('/invoices/generate-bulk', [InvoiceController::class, 'generateBulk'])->name('invoices.generate-bulk');

        // Payments (admin: verify/reject)
        Route::get('/payments',               [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}',     [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

        // Complaints
        Route::resource('complaints', ComplaintController::class)->except(['edit', 'update', 'destroy']);
        Route::post('/complaints/{complaint}/reply',          [ComplaintController::class, 'reply'])->name('complaints.reply');
        Route::patch('/complaints/{complaint}/update-status', [ComplaintController::class, 'updateStatus'])->name('complaints.update-status');

        // Reports
        Route::get('/reports',       [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf',   [ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');

        // Settings
        Route::get('/settings',  [SettingController::class, 'index'])->name('settings.index');
        Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

// ══════════════════════════════════════════════════════════════════
//  BACKWARD COMPAT — Redirect /dashboard ke admin.dashboard
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'admin', 'onboarding'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ══════════════════════════════════════════════════════════════════
//  TENANT — Hanya untuk role tenant
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'tenant'])->prefix('tenant')->name('tenant.')->group(function () {

    // Dashboard tenant
    Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('dashboard');

    // Tagihan tenant
    Route::get('/invoices',          [TenantInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [TenantInvoiceController::class, 'show'])->name('invoices.show');

    // Upload bukti bayar
    Route::get('/invoices/{invoice}/pay',  [TenantPaymentController::class, 'upload'])->name('payments.upload');
    Route::post('/invoices/{invoice}/pay', [TenantPaymentController::class, 'store'])->name('payments.store');

    // Keluhan tenant
    Route::get('/complaints',              [TenantComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/create',       [TenantComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints',             [TenantComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}',  [TenantComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{complaint}/reply', [TenantComplaintController::class, 'reply'])->name('complaints.reply');

    // Pengajuan sewa tenant
    Route::get('/applications',          [TenantRentalApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/create',   [TenantRentalApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications',         [TenantRentalApplicationController::class, 'store'])->name('applications.store');
});
