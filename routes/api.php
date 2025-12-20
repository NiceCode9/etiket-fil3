<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\MidtransCallbackController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\Api\AuthController;

// Public routes
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{slug}', [EventController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);

// Midtrans webhook callback
Route::post('/midtrans/callback', [MidtransCallbackController::class, 'handle']);

// Auth routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Scanner routes (protected)
Route::middleware(['auth:sanctum'])->group(function () {
    // QR Scanner
    Route::middleware(['role:super_admin,qr_scanner'])->group(function () {
        Route::post('/scan/qr-code', [ScanController::class, 'scanQRCode']);
        Route::get('/tickets/qr/{qrCode}', [ScanController::class, 'getTicketByQR']);
    });

    // Wristband Validator
    Route::middleware(['role:super_admin,wristband_validator'])->group(function () {
        Route::post('/scan/wristband', [ScanController::class, 'validateWristband']);
        Route::get('/tickets/wristband/{wristbandCode}', [ScanController::class, 'getTicketByWristband']);
    });
});
