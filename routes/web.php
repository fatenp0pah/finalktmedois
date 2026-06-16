<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\OfficerController;

// Default → login
Route::get('/', fn() => redirect()->route('login'));

// ── AUTH ───────────────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ── VENDOR — Module 1 (Registry) + Module 3 (Invoice) ─────────────────────────
Route::middleware(['auth', 'vendor'])->prefix('vendor')->name('vendor.')->group(function () {

    // Module 1 — Vendor Registry [KTMeDOIS-2026-V1-R100]
    Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile',   [VendorController::class, 'profile'])->name('profile');
    Route::post('/sync',     [VendorController::class, 'syncProfile'])->name('sync');

    // Module 2 — DO Submission (vendor side) [KTMeDOIS-2026-V1-R200]
    Route::prefix('do')->name('do.')->group(function () {
        Route::get('/dashboard', [DeliveryOrderController::class, 'dashboard'])->name('dashboard');
        Route::get('/create',    [DeliveryOrderController::class, 'create'])->name('create');
        Route::post('/store',    [DeliveryOrderController::class, 'store'])->name('store');
        Route::get('/status',    [DeliveryOrderController::class, 'status'])->name('status');
        Route::get('/report',    [DeliveryOrderController::class, 'report'])->name('report');
        Route::get('/detail/{doNo}', [DeliveryOrderController::class, 'detail'])->name('detail');
        Route::get('/notifications', [DeliveryOrderController::class, 'notifications'])->name('notifications');
    });

    // Module 3 — Invoice Submission [KTMeDOIS-2026-V1-R300]
    Route::prefix('invoice')->name('invoice.')->group(function () {
        Route::get('/',          fn() => view('vendor.invoice.index'))->name('index');
        Route::get('/submit',    fn() => view('vendor.invoice.create'))->name('create');
        Route::get('/{id}',      [VendorController::class, 'showInvoice'])->name('show');
    });
});

// ── OFFICER — Module 2 review side ────────────────────────────────────────────
Route::middleware(['auth', 'officer'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/dashboard',             [DeliveryOrderController::class, 'dashboard'])->name('dashboard');
    Route::get('/do/review',             [DeliveryOrderController::class, 'review'])->name('do.review');
    Route::post('/do/review/update',     [DeliveryOrderController::class, 'updateReview'])->name('do.update');
    Route::post('/do/start-review',      [DeliveryOrderController::class, 'startReview'])->name('do.startReview');
    Route::get('/do/status',             [DeliveryOrderController::class, 'status'])->name('do.status');
    Route::get('/do/report',             [DeliveryOrderController::class, 'report'])->name('do.report');
    Route::get('/do/detail/{doNo}',      [DeliveryOrderController::class, 'detail'])->name('do.detail');
    Route::get('/notifications',         [DeliveryOrderController::class, 'notifications'])->name('notifications');
    Route::get('/audit',                 [OfficerController::class, 'auditLog'])->name('audit');
    Route::get('/invoices',              [OfficerController::class, 'invoices'])->name('invoices');
});
