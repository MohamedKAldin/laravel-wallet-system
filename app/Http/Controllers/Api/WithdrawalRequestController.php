<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Notifications\NewWithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class WithdrawalRequestController extends Controller
{
    public function index(Request $request)
    {
        $withdrawals = $request->user()->wallet->transactions()
            ->where('type', 'withdrawal')
            ->latest()
            ->paginate(10);
        return response()->json($withdrawals);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();

        // Check if user has sufficient balance
        if ($user->wallet->balance < $request->amount) {
            return response()->json([
                'message' => 'Insufficient balance. Available balance: ' . number_format($user->wallet->balance, 2) . ' EGP. Please create a top-up request to add funds to your wallet before making this withdrawal request.',
                'available_balance' => $user->wallet->balance,
                'requested_amount' => $request->amount,
                'suggestion' => 'Create a top-up request to add funds to your wallet'
            ], 422);
        }

        $transaction = $user->wallet->transactions()->create([
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'status' => 'pending',
            'description' => 'User withdrawal request via API.',
        ]);

        // Hold the amount and deduct from balance
        $user->wallet->decrement('balance', $request->amount);
        $user->wallet->increment('held_balance', $request->amount);

        // Notify all admins about the new withdrawal request
        $allAdmins = Admin::all();
        Notification::send($allAdmins, new NewWithdrawalRequest($transaction));

        return response()->json([
            'message' => 'Withdrawal request submitted successfully',
            'transaction' => $transaction
        ], 201);
    }
} 