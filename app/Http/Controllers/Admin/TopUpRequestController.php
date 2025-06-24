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
        $admin = auth('admin')->user();

        if ($transaction->type !== 'top-up' || $transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Invalid transaction.');
        }

        // Prevent admin from approving their own request
        if ($transaction->isCreatedByAdmin($admin)) {
            return redirect()->back()->with('error', 'You cannot approve your own top-up request.');
        }

        $transaction->update(['status' => 'approved']);
        $transaction->wallet->increment('balance', $transaction->amount);
        $transaction->wallet->owner->notify(new TopUpRequestStatusUpdated($transaction));

        return redirect()->back()->with('success', 'Top-up request approved successfully.');
    }

    public function reject(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        $admin = auth('admin')->user();

        if ($transaction->type !== 'top-up' || $transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Invalid transaction.');
        }

        // Prevent admin from rejecting their own request
        if ($transaction->isCreatedByAdmin($admin)) {
            return redirect()->back()->with('error', 'You cannot reject your own top-up request.');
        }

        $transaction->update(['status' => 'rejected']);
        $transaction->wallet->owner->notify(new TopUpRequestStatusUpdated($transaction));

        return redirect()->back()->with('success', 'Top-up request rejected successfully.');
    }
}
