<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    use HasFactory;

    protected $primaryKey = 'Users_id';

    public function getUserPoints(){
        return $this->hasOne(user_points::class,'id');
    }

    public function getUserNotifications(){
        return $this->hasMany(notifications::class);
    }

    public function getUserOrders(){
        return $this->hasMany(orders::class);
    }

    public function getUserAddress(){
        return $this->belongsToMany(address::class,
        'useraddress',
        'Users_id',
        'address_id');
    }
}
