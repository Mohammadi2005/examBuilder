<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    protected $dateFormat = 'U'; 
    protected $fillable = [
        'mobile', 
        'code', 
        'name', 
        'gender', 
        'user_id',
        'expires_at', 
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'expires_at' => 'datetime',
    ];
    
    
    public function isExpired()
    {
        if (!$this->expires_at) {
            return true;
        }
        return $this->expires_at->isPast();
    }
}