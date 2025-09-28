<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StripeWebhookController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Stripe Webhook（認証不要）
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);