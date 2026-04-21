<?php
// routes/api.php

use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\WarehouseWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ==================== WAREHOUSE SYSTEM API ====================

// Webhook dari warehouse system (untuk update status order)
Route::post('/webhooks/warehouse-order-status', [WarehouseWebhookController::class, 'updateOrderStatus'])
    ->name('api.warehouse.webhook.status');

// External order creation (dari Simfati atau sistem lain)
Route::post('/orders', [OrderApiController::class, 'store'])
    ->name('api.orders.store');

// Get order status
Route::get('/orders/{orderId}', [OrderApiController::class, 'show'])
    ->name('api.orders.show');