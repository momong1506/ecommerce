<?php

use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API v1 routes
Route::prefix('v1')->group(function () {

    // Catalog Service Routes (Product Management)
    Route::prefix('catalog')->group(function () {
        // GET /api/v1/catalog/products - List all products
        Route::get('/products', [ProductController::class, 'index']);

        // GET /api/v1/catalog/products/{id} - Get single product
        Route::get('/products/{id}', [ProductController::class, 'show']);
    });

    // Checkout Service Routes (Order Management)
    Route::prefix('checkout')->group(function () {
        // POST /api/v1/checkout/orders - Create new order
        Route::post('/orders', [OrderController::class, 'store']);

        // GET /api/v1/checkout/orders/{orderNumber} - Get order by order number
        Route::get('/orders/{orderNumber}', [OrderController::class, 'showByOrderNumber']);
    });

    // Email Service Routes (Email Log Management)
    Route::prefix('email')->group(function () {
        // Email routes will be added in Phase 3
        // GET /api/v1/email/logs - Get email logs
        // POST /api/v1/email/logs/{id}/retry - Retry failed email
    });
});
