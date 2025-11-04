<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Balance\Controllers\BalanceController;

Route::post('deposit', [BalanceController::class, 'deposit']);
Route::post('withdraw', [BalanceController::class, 'withdraw']);
Route::post('transfer', [BalanceController::class, 'transfer']);
Route::get('balance/{userId}', [BalanceController::class, 'getBalance']);
