<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CryptoController;
use App\Http\Controllers\TransactionController;
Route::get('/create-wallet', [CryptoController::class, 'createWallet']);
Route::get('/get-balance/{balance}', [CryptoController::class, 'getBalance']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('crypto/send', [CryptoController::class, 'sendCrypto']);
    Route::post('crypto/swap', [CryptoController::class, 'swapCrypto']);
    Route::get('transactions', [TransactionController::class, 'getTransactionHistory']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


