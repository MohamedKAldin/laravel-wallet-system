<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
use App\Models\ReferralCode;
use App\Models\Transaction;
use Illuminate\Support\Str;

class ReferralService
{
    private $bonusAmount = 10;

    public function generateCode($owner)
    {
        // Deactivate all previous codes for this owner
        $ownerType = $owner instanceof Admin ? 'admin_id' : 'user_id';
        ReferralCode::where($ownerType, $owner->id)
                   ->where('status', 'active')
                   ->update(['status' => 'inactive']);

        // Generate new code
        $code = Str::random(8);
        
        // Ensure code is unique
        while (ReferralCode::where('code', $code)->exists()) {
            $code = Str::random(8);
        }

        return $owner->referralCodes()->create([
            'code' => $code,
            'status' => 'active',
        ]);
    }

    public function applyCode(User $user, $code)
    {
        $referralCode = ReferralCode::where('code', $code)
                                   ->where('status', 'active')
                                   ->with(['admin', 'user'])
                                   ->first();

        if (!$referralCode) {
            return false;
        }

        // Determine the referrer based on which ID is set
        if ($referralCode->admin_id) {
            $referrer = $referralCode->admin;
        } elseif ($referralCode->user_id) {
            $referrer = $referralCode->user;
        } else {
            return false;
        }

        if (!$referrer) {
            return false;
        }

        // Give bonus to the new user
        $user->wallet()->increment('balance', $this->bonusAmount);
        
        // Create transaction record for user
        $user->wallet->transactions()->create([
            'type' => 'referral_bonus',
            'amount' => $this->bonusAmount,
            'status' => 'approved',
            'description' => 'Referral bonus from ' . $referrer->name . ' (Code: ' . $code . ')',
        ]);

        // Give bonus to the referrer
        $referrer->wallet()->increment('balance', $this->bonusAmount);
        
        // Create transaction record for referrer
        $referrer->wallet->transactions()->create([
            'type' => 'referral_bonus',
            'amount' => $this->bonusAmount,
            'status' => 'approved',
            'description' => 'Referral bonus for referring ' . $user->name . ' (Code: ' . $code . ')',
        ]);

        return true;
    }

    public function getLatestCode($owner)
    {
        return ReferralCode::getLatestForOwner($owner);
    }
} 