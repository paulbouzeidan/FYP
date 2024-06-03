<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vonage\Client;

class SMSController extends Controller
{
    protected $vonageClient;

    public function __construct(Client $vonageClient)
    {
        $this->vonageClient = $vonageClient;
    }

    public function sendSMS()
    {
        $response = $this->vonageClient->sms()->send(
            new \Vonage\SMS\Message\SMS("96171570760", "BRAND_NAME", 'A text message sent using the Vonage SMS API')
        );

        $message = $response->current();

        if ($message->getStatus() == 0) {
            return response()->json(['message' => 'The message was sent successfully'], 200);
        } else {
            return response()->json(['message' => 'The message failed with status: ' . $message->getStatus()], 500);
        }
    }
}
