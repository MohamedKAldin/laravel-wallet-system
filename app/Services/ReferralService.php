<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
use App\Models\ReferralCode;
use Illuminate\Support\Str;

class ReferralService
{
    public function generateCode($owner)
    {
        $code = Str::random(8);

        return $owner->referralCodes()->create([
            'code' => $code,
        ]);
    }

    public function applyCode(User $user, $code)
    {
        $referralCode = ReferralCode::where('code', $code)->where('status', 'active')->first();

        if (!$referralCode) {
            return false;
        }

        $referrer = $referralCode->owner;

        // Give bonus to the new user
        $user->wallet()->increment('balance', 10);

        // Give bonus to the referrer
        $referrer->wallet()->increment('balance', 10);

        $referralCode->update(['status' => 'used']);

        return true;
    }
} 