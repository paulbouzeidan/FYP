<?php

namespace App\Http\Controllers;

use App\Models\address;
use App\Models\users;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function index(){
        $obj = Users::find(1);
        return $obj;
    }
}
