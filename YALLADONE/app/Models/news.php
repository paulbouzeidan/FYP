<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class news extends Model
{
    use HasFactory;

    protected $primaryKey = 'news_id';
    protected $fillable = [

        'news_description',
        'news_date',
        'Title',
        'image_path'

    ];

}
