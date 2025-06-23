<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Admin;
use App\Notifications\NewWithdrawalRequest;
use App\Notifications\WithdrawalRequestStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class WithdrawalRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Transaction::class);

        $transactions = Transaction::where('type', 'withdrawal')->latest()->paginate(10);

        return response()->json($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $admin = $request->user();

        if ($admin->wallet->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance.'], 400);
        }

        $transaction = $admin->wallet->transactions()->create([
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'status' => 'pending',
            'description' => 'Admin withdrawal request.',
        ]);

        $admin->wallet->decrement('balance', $request->amount);
        $admin->wallet->increment('held_balance', $request->amount);

        $otherAdmins = Admin::where('id', '!=', $admin->id)->get();
        Notification::send($otherAdmins, new NewWithdrawalRequest($transaction));

        return response()->json($transaction, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        return response()->json($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function approve(Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        if ($transaction->type !== 'withdrawal' || $transaction->status !== 'pending') {
            return response()->json(['message' => 'Invalid transaction.'], 400);
        }

        $transaction->update(['status' => 'approved']);

        $transaction->wallet->decrement('held_balance', $transaction->amount);

        $transaction->wallet->owner->notify(new WithdrawalRequestStatusUpdated($transaction));

        return response()->json($transaction);
    }

    public function reject(Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        if ($transaction->type !== 'withdrawal' || $transaction->status !== 'pending') {
            return response()->json(['message' => 'Invalid transaction.'], 400);
        }

        $transaction->update(['status' => 'rejected']);

        $transaction->wallet->increment('balance', $transaction->amount);
        $transaction->wallet->decrement('held_balance', $transaction->amount);

        $transaction->wallet->owner->notify(new WithdrawalRequestStatusUpdated($transaction));

        return response()->json($transaction);
    }
}
