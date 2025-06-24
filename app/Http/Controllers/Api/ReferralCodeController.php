<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use Illuminate\Http\Request;

class ReferralCodeController extends Controller
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    public function generate(Request $request)
    {
        $user = $request->user();
        
        $referralCode = $this->referralService->generateCode($user);

        return response()->json([
            'message' => 'Referral code generated successfully',
            'code' => $referralCode->code,
        ]);
    }

    public function show(Request $request)
    {
        $user = $request->user();
        
        $referralCode = $this->referralService->getLatestCode($user);

        if (!$referralCode) {
            return response()->json([
                'message' => 'No active referral code found',
                'code' => null,
            ]);
        }

        return response()->json([
            'code' => $referralCode->code,
            'created_at' => $referralCode->created_at,
        ]);
    }
} 