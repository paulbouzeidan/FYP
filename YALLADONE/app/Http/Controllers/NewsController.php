<?php

namespace App\Http\Controllers;
use App\Models\news;

use App\Notifications\NewsNotification;
use Illuminate\Http\Request;

class NewsController extends Controller
{
public function getNews(){
    $news = news::all();

    return response()->json($news, 200);

}
}
