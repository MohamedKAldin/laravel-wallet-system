<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'status',
        'user_id',
        'admin_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function getOwnerAttribute()
    {
        return $this->admin_id ? $this->admin : $this->user;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLatestForOwner($query, $owner)
    {
        $ownerType = $owner instanceof Admin ? 'admin_id' : 'user_id';
        return $query->where($ownerType, $owner->id)
                    ->where('status', 'active')
                    ->latest('created_at');
    }

    public static function getLatestForOwner($owner)
    {
        return static::latestForOwner($owner)->first();
    }
}
