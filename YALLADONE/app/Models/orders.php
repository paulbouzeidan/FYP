<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    use HasFactory;
    protected $primaryKey ='order_id';


    public function user()
    {
        return $this->belongsTo(users::class, 'user_id', 'Users_id');
    }

    public function payment()
    {
        return $this->belongsTo(payment::class, 'Payment_id', 'payment_id');
    }

    public function service_form()
    {
        return $this->belongsTo(services_form::class, 'Form_id', 'form_id');
    }


    protected $fillable = [
        'user_id',
        'payment_id',
        'form_id',
        'status'
    ];
}
