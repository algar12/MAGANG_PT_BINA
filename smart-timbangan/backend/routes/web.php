<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\Auth\OperatorLoginController;

// ─── Landing Page & Operator Login ───────────────────────────────────────────
Route::get('/', [OperatorLoginController::class, 'index'])->name('login.operator');
Route::post('/login', [OperatorLoginController::class, 'login'])->name('operator.login');
Route::post('/logout', [OperatorLoginController::class, 'logout'])->name('operator.logout');

// ─── Operator Dashboard (setelah login) ──────────────────────────────────────
Route::get('/dashboard', [OperatorLoginController::class, 'dashboard'])->name('operator.dashboard');

// ─── Production Costing (operator) ───────────────────────────────────────────
Route::get('/production/costing/{order_id}', [ProductionController::class, 'showCosting']);
