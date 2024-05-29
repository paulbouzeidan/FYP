<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class services extends Model
{
    use HasFactory;
    protected $primaryKey ='service_id';
    public function getServicers_form(){
        return $this->hasMany(services_form::class);
    }


    protected $fillable = [

        'image',
        'category',
        'price',
        'service_name',
        'service_description',
        'isEmergency',
        'IsFav',

    ];
}
