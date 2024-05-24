<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Mail\YallaDoneMail;
use Illuminate\Support\Facades\Mail;


class sendEmail extends Controller
{
    public function send(Request $request)
    {
        try {
            $request->validate([

                'body' => 'required',

            ]);

            $mailData = [

                'body' => $request->body,

            ];

            // Retrieve the authenticated user's email
            $loggedInUserEmail = auth()->user()->email;

            // Send the email
            Mail::to('YallaDone@gmail.com')->send(new TestMail($mailData, $loggedInUserEmail));

            return response()->json([
                'status' => true,
                'message' => 'Email sent!',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function YallaDoneSend(Request $request)
    {
        try {
            // Validate the request
           
            // Retrieve the authenticated user
            $user = auth()->user();

            // Check if the user is authenticated and the user_name property exists
            if ($user && isset($user->user_name)) {
                $mailData = $user->user_name;
                $email =$user->email;
                // Send the email
                Mail::to($email)->send(new YallaDoneMail($mailData));

                return response()->json([
                    'status' => true,
                    'message' => 'Email sent!',
                ], 200);
            } else {
                // Handle the case where the user is not authenticated or user_name is not set
                return response()->json([
                    'status' => false,
                    'message' => 'User is not authenticated or user_name is not set',
                ], 401);
            }
        } catch (\Throwable $th) {
            // Catch any other exceptions and return a server error response
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }




}
