<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController as WebEventController;
use App\Http\Controllers\CheckoutController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Events
Route::get('/events', [WebEventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [WebEventController::class, 'show'])->name('events.show');

// Checkout - Process order
Route::post('/checkout/{event}', [CheckoutController::class, 'process'])->name('checkout.process');

// Payment Routes
Route::prefix('payment')->name('payment.')->group(function () {
    // Payment waiting page (shows Midtrans popup)
    Route::get('/{orderNumber}/waiting', [CheckoutController::class, 'paymentWaiting'])->name('waiting');

    // Payment success page
    Route::get('/{orderNumber}/success', [CheckoutController::class, 'paymentSuccess'])->name('success');

    // Payment failed page
    Route::get('/{orderNumber}/failed', [CheckoutController::class, 'paymentFailed'])->name('failed');

    // Cancel payment (when user closes Midtrans popup)
    Route::get('/{orderNumber}/cancel', [CheckoutController::class, 'cancelPayment'])->name('cancel');

    // Check payment status (AJAX)
    Route::get('/{orderNumber}/check-status', [CheckoutController::class, 'checkStatus'])->name('check-status');

    // Midtrans finish redirect
    Route::get('/{orderNumber}/finish', [\App\Http\Controllers\Api\MidtransCallbackController::class, 'finish'])->name('finish');
});

// Order
Route::get('/order/{orderNumber}', [CheckoutController::class, 'show'])->name('order.show');

// Test email (for development only - remove in production)
Route::get('/order/{id}/test-email', [CheckoutController::class, 'testEmail'])->name('order.test-email');
