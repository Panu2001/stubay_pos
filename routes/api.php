<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SyncController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/sync/products', [SyncController::class, 'pullProducts']);
Route::post('/sync/orders', [SyncController::class, 'pushOrders']);
