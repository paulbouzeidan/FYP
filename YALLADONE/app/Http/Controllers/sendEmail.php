<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\TestMail;

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

}
