<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class users extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'Users_id';



    public function getUserPoints()
    {
        return $this->hasOne(user_points::class, 'id');
    }

    public function getUserNotifications()
    {
        return $this->hasMany(notifications::class);
    }

    public function getUserOrders()
    {
        return $this->hasMany(orders::class);
    }

    public function getUserAddress()
    {
        return $this->hasMany(address::class, 'user_id', 'Users_id');
    }


    protected $fillable = [

        'user_name',
        'user_lastname',
        'birthday',
        'phone_number',
        'email',
        'password',
        'is_verified'

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
        'password' => 'hashed',
    ];
}

