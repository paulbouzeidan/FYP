<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vonage\Client;
use App\Models\orders;
class SMSController extends Controller
{
    protected $vonageClient;

    public function __construct(Client $vonageClient)
    {
        $this->vonageClient = $vonageClient;
    }

    public function sendSMS(Request $request)
    {
        // Retrieve user from bearer token
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validate the order ID from the request
        $orderId = $request->input('order_id');
        if (!$orderId) {
            return response()->json(['message' => 'Order ID is required'], 400);
        }

        // Retrieve the order for the authenticated user
        $order = orders::where('order_id', $orderId)->where('user_id', $user->Users_id)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Check if the message has already been sent
        if ($order->isOrderMessage === 'true') {
            return response()->json(['message' => 'The message has already been sent'], 200);
        }

        // Send the SMS message
        $userNumber = $user->phone_number;
        $response = $this->vonageClient->sms()->send(
            new \Vonage\SMS\Message\SMS($userNumber, "YallaDone", 'The distance to your order is now less than 1 km, indicating that it is very close to your location.')
        );

        $message = $response->current();

        if ($message->getStatus() == 0) {
            // Update the order to indicate the message has been sent
            $order->update(['isOrderMessage' => 'true']);
            return response()->json(['message' => 'The message was sent successfully'], 200);
        } else {
            return response()->json(['message' => 'The message failed with status: ' . $message->getStatus()], 500);
        }
    }

}
