<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_points extends Model
{
    use HasFactory;

   
    public function getUser(){
        return $this->belongsTo(users::class,'user_id','id');
    }
}
