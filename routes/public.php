<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('landing');
Route::get('/katalog/{tiketPaket}', [KatalogController::class, 'show'])->name('katalog.show');

Route::get('/payment', [PaymentController::class, 'index'])->name('demo-payment.index');
Route::post('/payment', [PaymentController::class, 'pay'])->name('demo-payment.pay');
Route::post('/payment/check', [PaymentController::class, 'check'])->name('demo-payment.check');
