<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'status',
        'description',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function scopeTopUp($query)
    {
        return $query->where('type', 'top-up');
    }

    public function scopeWithdrawal($query)
    {
        return $query->where('type', 'withdrawal');
    }

    public function scopeReferralBonus($query)
    {
        return $query->where('type', 'referral_bonus');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function isCreatedByAdmin($admin)
    {
        return $this->wallet && $this->wallet->admin && $this->wallet->admin->id === $admin->id;
    }
}
