<?php

use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/midtrans', [PaymentWebhookController::class, 'handle'])
    ->name('payment.webhook');
