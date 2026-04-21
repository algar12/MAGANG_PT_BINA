<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/production/costing/{order_id}', [ProductionController::class, 'showCosting']);
