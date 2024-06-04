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


    public function service()
    {
        return $this->hasOneThrough(
            services::class,
            services_form::class,
            'form_id', // Foreign key on services_form table
            'service_id', // Foreign key on services table
            'Form_id', // Local key on orders table
            'service_id' // Local key on services_form table
        );
    }


    protected $fillable = [
        'user_id',
        'payment_id',
        'form_id',
        'status'
    ];
}
