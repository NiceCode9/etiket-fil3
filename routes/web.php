<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController as WebEventController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TrackingController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Events
Route::get('/events', [WebEventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [WebEventController::class, 'show'])->name('events.show');

// Tracking
Route::prefix('tracking')->name('tracking.')->group(function () {
    Route::get('/', [TrackingController::class, 'index'])->name('index');
    Route::post('/track', [TrackingController::class, 'track'])->name('track');
    Route::get('/{orderNumber}', [TrackingController::class, 'show'])->name('detail');
    Route::get('/{orderNumber}/download-invoice', [TrackingController::class, 'downloadInvoice'])->name('download-invoice');
});

// Checkout - Process order
Route::post('/checkout/{event}', [CheckoutController::class, 'process'])->name('checkout.process');

// Online Payment Routes
// Route::prefix('payment')->name('payment.')->group(function () {
//     // Payment waiting page (shows Midtrans popup)
//     Route::get('/{orderNumber}/waiting', [CheckoutController::class, 'paymentWaiting'])->name('waiting');

//     // Payment success page
//     Route::get('/{orderNumber}/success', [CheckoutController::class, 'paymentSuccess'])->name('success');

//     // Payment failed page
//     Route::get('/{orderNumber}/failed', [CheckoutController::class, 'paymentFailed'])->name('failed');

//     // Cancel payment (when user closes Midtrans popup)
//     Route::get('/{orderNumber}/cancel', [CheckoutController::class, 'cancelPayment'])->name('cancel');

//     // Check payment status (AJAX)
//     Route::get('/{orderNumber}/check-status', [CheckoutController::class, 'checkStatus'])->name('check-status');

//     // Download invoice
//     Route::get('/{orderNumber}/download-invoice', [CheckoutController::class, 'downloadInvoice'])->name('download-invoice');

//     // Midtrans finish redirect
//     Route::get('/{orderNumber}/finish', [\App\Http\Controllers\Api\MidtransCallbackController::class, 'finish'])->name('finish');
// });


// Offline Payment Routes
Route::prefix('payment')->name('payment.')->group(function () {
    // Payment waiting page (shows Midtrans popup)
    Route::get('/{orderNumber}/waiting', [CheckoutController::class, 'offlinePaymentWaiting'])->name('waiting');

    // Payment success page
    Route::get('/{orderNumber}/success', [CheckoutController::class, 'paymentSuccess'])->name('success');

    // Payment failed page
    Route::get('/{orderNumber}/failed', [CheckoutController::class, 'paymentFailed'])->name('failed');

    // Cancel payment (when user closes Midtrans popup)
    Route::get('/{orderNumber}/cancel', [CheckoutController::class, 'cancelPayment'])->name('cancel');

    // Check payment status (AJAX)
    Route::get('/{orderNumber}/check-status', [CheckoutController::class, 'checkStatus'])->name('check-status');

    // Download invoice
    Route::get('/{orderNumber}/download-invoice', [CheckoutController::class, 'downloadInvoice'])->name('download-invoice');

    // Midtrans finish redirect
    Route::get('/{orderNumber}/finish', [\App\Http\Controllers\Api\MidtransCallbackController::class, 'finish'])->name('finish');
});

// Order
Route::get('/order/{orderNumber}', [CheckoutController::class, 'show'])->name('order.show');

// Test email (for development only - remove in production)
Route::get('/order/{id}/test-email', [CheckoutController::class, 'testEmail'])->name('order.test-email');
