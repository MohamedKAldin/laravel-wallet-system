<?php

namespace App\Http\Controllers\Admin;

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
        $admin = $request->user();
        
        $referralCode = $this->referralService->generateCode($admin);

        return redirect()->back()->with('success', 'Referral code generated successfully: ' . $referralCode->code);
    }

    public function show(Request $request)
    {
        $admin = $request->user();
        
        $referralCode = $this->referralService->getLatestCode($admin);

        if (!$referralCode) {
            return redirect()->back()->with('info', 'No active referral code found. Generate one to get started.');
        }

        return redirect()->back()->with('info', 'Your current referral code: ' . $referralCode->code);
    }
} 