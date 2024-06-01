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


    public function getCarServices()
    {
        return $this->where('category', 'Car')->get();
    }


    public function getTransportationServices()
    {
        return $this->where('category', 'Transportation')->get();
    }


    public function getPaperworkServices()
    {
        return $this->where('category', 'Paperwork')->get();
    }


    public function getDeliveryServices()
    {
        return $this->where('category', 'Delivery')->get();
    }


    protected $fillable = [

        'image',
        'category',
        'price',
        'service_name',
        'service_description',
        'isEmergency',
      

    ];
}
