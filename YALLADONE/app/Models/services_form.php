<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class services_form extends Model
{
    use HasFactory;

    public function getOrder(){
        return $this->hasOne(orders::class);
    }

    public function getService(){
        return $this->belongsTo(services::class,'service_id','form_id');

        
    }
}
