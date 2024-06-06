<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\users;
use Http;
class FrontendNotification extends Controller
{

    public function saveToken(Request $request)
    {
        $user = auth()->user();
        $user->expo_push_token = $request->token;
        $user->save();

        return response()->json(['success' => true]);
    }



    public function sendNotification($title, $message, $token)
{
    $expoUrl = 'https://exp.host/--/api/v2/push/send';

    $response = Http::post($expoUrl, [
        'to' => $token,
        'sound' => 'default',
        'title' => $title,
        'body' => $message,
    ]);

    return $response->json();
}
}
