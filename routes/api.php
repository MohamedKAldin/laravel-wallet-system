<?php

use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\TopUpRequestController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ReferralCodeController;
use App\Http\Controllers\Api\WithdrawalRequestController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\ReferralController;
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

Route::post('/register', RegisterController::class);
Route::post('/login', LoginController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Top-up requests
    Route::post('/top-up-requests', [TopUpRequestController::class, 'store']);
    Route::get('/top-up-requests', [TopUpRequestController::class, 'index']);

    // Withdrawal requests
    Route::post('/withdrawal-requests', [WithdrawalRequestController::class, 'store']);
    Route::get('/withdrawal-requests', [WithdrawalRequestController::class, 'index']);

    // Wallet
    Route::get('/wallet', [WalletController::class, 'show']);

    // Referral codes
    Route::post('/referral-codes/generate', [ReferralCodeController::class, 'generate']);
    Route::get('/referral-codes/show', [ReferralCodeController::class, 'show']);

    // Referrals
    Route::get('/referrals', [ReferralController::class, 'index']);
});
