<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction;
use App\Models\Admin;
use App\Notifications\NewTopUpRequest;
use Illuminate\Support\Facades\Notification;

class TopUpRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transactions = $request->user()->wallet->transactions()->where('type', 'top-up')->latest()->paginate(10);

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

        $transaction = $request->user()->wallet->transactions()->create([
            'type' => 'top-up',
            'amount' => $request->amount,
            'status' => 'pending',
            'description' => 'User top-up request.',
        ]);

        // Notify all admins about the new top-up request
        $allAdmins = Admin::all();
        Notification::send($allAdmins, new NewTopUpRequest($transaction));

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
}
