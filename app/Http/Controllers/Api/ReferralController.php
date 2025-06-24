<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReferralCode;
use App\Models\User;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Get all codes owned by this user
        $codes = $user->referralCodes()->pluck('code');
        // Find users who registered with any of these codes
        $referredUsers = User::whereHas('wallet.transactions', function($q) use ($codes) {
            $q->where('type', 'referral_bonus')
              ->where(function($query) use ($codes) {
                  foreach ($codes as $code) {
                      $query->orWhere('description', 'like', "%Code: $code%") ;
                  }
              });
        })->pluck('name');
        return response()->json(['referred_users' => $referredUsers]);
    }
} 