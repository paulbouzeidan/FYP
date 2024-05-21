<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    use HasFactory;
    protected $primaryKey = 'address_id';


    public function getUser()
    {
        return $this->belongsTo(users::class, 'user_id', 'Users_id');
    }

    protected $fillable = [

        'longitude',
        'latitude',
        'user_id',

        'location_type',
        'name',
        'district',
        'city',
        'street',
        'building',
        'floor' ,
        'additional_info' ,


    ];

}


