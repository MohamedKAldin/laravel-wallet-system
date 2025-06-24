<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show(Request $request)
    {
        $wallet = $request->user()->wallet;
        return response()->json([
            'balance' => $wallet->balance,
            'held_balance' => $wallet->held_balance,
        ]);
    }
} 