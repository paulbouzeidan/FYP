<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavService extends Model
{
    use HasFactory;

    protected $fillable = [

        'IsFav',
        'user_id',
        'service_idF',
        'IsFav'

    ];

}
