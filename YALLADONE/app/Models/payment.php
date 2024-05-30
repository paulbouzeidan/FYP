<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    use HasFactory;
    protected $primaryKey ='payment_id';


    public function getOrdersPayment(){
        return $this->hasOne(orders::class);
    }

    protected $fillable = [

        'user_id',
        'type',
        'service_name',
        'price'




    ];





}
