<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
    use HasFactory;

    public function getUser(){
        return $this->belongsTo(users::class,'Users_id','notification_id');
    }
}
