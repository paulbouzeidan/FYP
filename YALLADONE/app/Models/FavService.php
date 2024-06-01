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


    protected $primaryKey = 'fav_service_id';

    // Define the relationship with the Service model
    public function service()
    {
        return $this->belongsTo(services::class, 'service_id', 'service_id');
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(users::class, 'user_id', 'Users_id');
    }

}
