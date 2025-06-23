<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
        'held_balance',
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getOwnerAttribute()
    {
        return $this->user ?? $this->admin;
    }
}
