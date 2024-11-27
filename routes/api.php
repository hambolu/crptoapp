<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CryptoController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Middleware\ApiGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Authentication Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/set-transaction-pin', [AuthController::class, 'setTransactionPin']);


Route::middleware('auth:sanctum')->group(function () {
    // Crypto Routes
    Route::get('/create-wallet', [CryptoController::class, 'createWallet']);
    Route::get('/get-balance/{balance}', [CryptoController::class, 'getBalance']);

    // Authentication Routes
    Route::post('logout', [AuthController::class, 'logout']);

    // Crypto Transaction Routes
    Route::post('transaction/send', [CryptoController::class, 'sendCrypto']);
    Route::post('transaction/swap', [CryptoController::class, 'swapCrypto']);
    Route::get('transactions', [TransactionController::class, 'getTransactionHistory']);
    Route::get('transaction/internal', [TransactionController::class, 'sendInternalTransaction']);

    // Wallet Routes
    Route::get('wallet', [WalletController::class, 'getWallet']);

    //user
    Route::get('/user/{uuid}', [UserController::class, 'getUserByUUID']);

});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


