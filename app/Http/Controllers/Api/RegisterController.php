<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReferralCode;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'referral_code' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check referral code if provided
        if ($request->referral_code && !empty($request->referral_code)) 
        {
            // Check if referral code exists and is active
            $referralCode = ReferralCode::where('code', $request->referral_code)
                                       ->where('status', 'active')
                                       ->first();

            if (!$referralCode) {
                return response()->json([
                    'message' => 'Invalid or inactive referral code'
                ], 422);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->wallet()->create();

        // Apply referral code if provided and valid
        if ($request->referral_code && !empty($request->referral_code)) 
        {
            $this->referralService->applyCode($user, $request->referral_code);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'User registered successfully' . ($request->referral_code && !empty($request->referral_code) ? ' with valid referral code' : ''),
        ]);
    }
}
