<?php

use App\Http\Controllers\Admin\TopUpRequestController;
use App\Http\Controllers\Admin\WithdrawalRequestController;
use Illuminate\Support\Facades\Route;

Route::get('top-up-requests', [TopUpRequestController::class, 'index'])->name('top-up-requests.index');
Route::get('top-up-requests/{transaction}', [TopUpRequestController::class, 'show'])->name('top-up-requests.show');
Route::post('top-up-requests/{transaction}/approve', [TopUpRequestController::class, 'approve'])->name('top-up-requests.approve');
Route::post('top-up-requests/{transaction}/reject', [TopUpRequestController::class, 'reject'])->name('top-up-requests.reject');

// Withdrawal requests
Route::get('withdrawal-requests', [WithdrawalRequestController::class, 'index'])->name('withdrawal-requests.index');
Route::post('withdrawal-requests', [WithdrawalRequestController::class, 'store'])->name('withdrawal-requests.store');
Route::get('withdrawal-requests/{transaction}', [WithdrawalRequestController::class, 'show'])->name('withdrawal-requests.show');
Route::post('withdrawal-requests/{transaction}/approve', [WithdrawalRequestController::class, 'approve'])->name('withdrawal-requests.approve');
Route::post('withdrawal-requests/{transaction}/reject', [WithdrawalRequestController::class, 'reject'])->name('withdrawal-requests.reject'); 