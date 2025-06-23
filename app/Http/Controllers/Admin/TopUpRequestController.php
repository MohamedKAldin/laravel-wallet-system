<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Notifications\TopUpRequestStatusUpdated;
use Illuminate\Http\Request;

class TopUpRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Transaction::class);

        $transactions = Transaction::where('type', 'top-up')->latest()->paginate(10);

        return response()->json($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

        if ($transaction->type !== 'top-up' || $transaction->status !== 'pending') {
            return response()->json(['message' => 'Invalid transaction.'], 400);
        }

        $transaction->update(['status' => 'approved']);

        $transaction->wallet->increment('balance', $transaction->amount);

        $transaction->wallet->owner->notify(new TopUpRequestStatusUpdated($transaction));

        return response()->json($transaction);
    }

    public function reject(Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        if ($transaction->type !== 'top-up' || $transaction->status !== 'pending') {
            return response()->json(['message' => 'Invalid transaction.'], 400);
        }

        $transaction->update(['status' => 'rejected']);

        $transaction->wallet->owner->notify(new TopUpRequestStatusUpdated($transaction));

        return response()->json($transaction);
    }
}
