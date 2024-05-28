<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
class Otp extends Model
{
    use HasFactory;



    protected $fillable = [
        'user_id',
        'email',
        'otp',
        'expires_at',
        'created_at',

    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

}
