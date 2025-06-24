<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'admin_id');
    }

    public function referralCodes()
    {
        return $this->hasMany(ReferralCode::class, 'admin_id');
    }

    public function scopeWithReferredUsers($query)
    {
        return $query->with(['wallet.transactions' => function($query) {
            $query->referralBonus()->approved();
        }]);
    }

    public function scopeWithApprovedWithdrawals($query)
    {
        return $query->with(['wallet.transactions' => function($query) {
            $query->withdrawal()->approved();
        }]);
    }
}
