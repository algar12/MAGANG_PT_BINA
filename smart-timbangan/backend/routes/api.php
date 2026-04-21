<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProductionController;
use App\Models\Material;
use App\Models\ProductionOrder;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ─── IoT Sensor ──────────────────────────────────────────────────────────────
Route::post('/sensor/weight',          [DeviceController::class, 'storeWeight']);
Route::get('/weight-live/{device_id}', [DeviceController::class, 'getLiveWeight']);

// ─── Production ───────────────────────────────────────────────────────────────
Route::get('/costing-live/{order_id}', [ProductionController::class, 'getLiveCosting']);

// ─── Operator React API ───────────────────────────────────────────────────────

// Daftar semua material (Bahan Baku)
Route::get('/materials', function () {
    $materials = Material::orderBy('nama_produk')->get([
        'id', 'kode_produk', 'nama_produk', 'uom_dasar', 'standart_cost', 'is_active'
    ]);
    return response()->json(['data' => $materials]);
});

// Daftar production orders yang aktif (In Progress) untuk operator
Route::get('/production-orders/active', function () {
    $orders = ProductionOrder::with('formula')
        ->whereIn('status', ['Draft', 'In Progress'])
        ->orderByDesc('start_date')
        ->get();
    return response()->json(['data' => $orders]);
});
