<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    use HasFactory;

    public function getUser(){
        return $this->belongsTo(users::class,'Users_id','order_id');
    }


    public function getPayment(){
        return $this->belongsTo(payment::class,'payment_id','order_id');

        
    }

    public function getService_form(){
        return $this->belongsTo(services_form::class,'form_id','order_id');

        
    }
}
