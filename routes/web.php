<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController as WebEventController;
use App\Http\Controllers\CheckoutController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [WebEventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [WebEventController::class, 'show'])->name('events.show');
Route::get('/checkout/{event}', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/{event}', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/order/{orderNumber}', [CheckoutController::class, 'show'])->name('order.show');
