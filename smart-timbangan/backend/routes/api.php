<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/sensor/weight', [DeviceController::class, 'storeWeight']);

use App\Http\Controllers\ProductionController;
Route::get('/costing-live/{order_id}', [ProductionController::class, 'getLiveCosting']);
