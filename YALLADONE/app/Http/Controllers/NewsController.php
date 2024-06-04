<?php

namespace App\Http\Controllers;
use App\Models\news;
use App\Models\users;

use Illuminate\Http\Request;

class NewsController extends Controller
{
public function getNews(){
    $news = news::all();

    return response()->json($news, 200);

}



public function storeNews(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'news_description' => 'required|string|max:255',
        'news_date' => 'nullable|date',
        'Title' => 'required|string|max:255'
    ]);

    try {
        // Create and store the news in the database
        $news = news::create([
            'news_description' => $validatedData['news_description'],
            'news_date' => $validatedData['news_date'],
            'Title' => $validatedData['Title']
        ]);

        $users = users::all();
        $info = $news;

        // Notify each user individually
        foreach ($users as $user) {
            $user->notify(new \App\Notifications\NewsNotification($info));
        }

        return response()->json([
            'status' => true,
            'message' => 'News created successfully',
            'news' => $news
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}
