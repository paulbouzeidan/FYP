<?php

namespace App\Http\Controllers;
use App\Models\news;
use App\Models\users;
use App\Models\services;
use Illuminate\Support\Facades\Log;
//this controller is for services and news
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
        'Title' => 'required|string|max:255',
        'image' => 'nullable|image|max:4096'
    ]);

    try {
        // Handle the image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            Log::info('Image file found');
            $imagePath = $request->file('image')->store('news_images', 'public');
            Log::info('Image stored at: ' . $imagePath);
        } else {
            Log::info('No image file found in the request');
        }

        // Create and store the news in the database
        $news = news::create([
            'news_description' => $validatedData['news_description'],
            'news_date' => $validatedData['news_date'],
            'Title' => $validatedData['Title'],
            'image_path' => $imagePath // Store the image path
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

public function storeService(Request $request)
{

    try {
    // Validate the request data
    $validatedData = $request->validate([
        'image' => 'image|max:2048', // Validate the image
        'category' => 'required|string|max:255',
        'price' => 'required|integer',
        'service_name' => 'required|string|max:255|unique:services,service_name',
        'service_description' => 'required|string|max:255',
        'isEmergency' => 'boolean'
    ]);

        // Handle the image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            Log::info('Image file found');
            $imagePath = $request->file('image')->store('service_images', 'public');
            Log::info('Image stored at: ' . $imagePath);
        } else {
            Log::info('No image file found in the request');
        }

        // Create and store the service in the database
        $service = services::create([
            'image' => $imagePath,
            'category' => $validatedData['category'],
            'price' => $validatedData['price'],
            'service_name' => $validatedData['service_name'],
            'service_description' => $validatedData['service_description'],
            'isEmergency' => $validatedData['isEmergency'] ?? false
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Service created successfully',
            'service' => $service
        ], 201);

    } catch (\Exception $e) {
        Log::error('Error: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

}
