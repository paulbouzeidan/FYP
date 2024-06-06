<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class services_form extends Model
{
    use HasFactory;
    protected $primaryKey = 'form_id';
    public function getOrder(){
        return $this->hasOne(orders::class);
    }

    public function getService(){
        return $this->belongsTo(services::class,'service_id','form_id');


    }

    public function service()
    {
        return $this->belongsTo(services::class, 'service_id', 'service_id');
    }

    public function services()
    {
        return $this->belongsTo(services::class, 'Service_id', 'service_id');
    }




    protected $fillable = [

            'user_id',
            'Service_id',
            'service_date',
            'user_name',
            'user_lastname',
            'email',
            'phone_number',
            'location',
            'additional_info'


    ];
}
