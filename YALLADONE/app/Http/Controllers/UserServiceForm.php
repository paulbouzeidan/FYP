<?php

namespace App\Http\Controllers;

use App\Models\services_form;
use App\Models\payment;
use App\Models\orders;
use App\Models\user_points;
use App\Notifications\OrderNotification;

;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\services;
use Stripe\Checkout\Session;


class UserServiceForm extends Controller
{

    public function StoreUserServiceForm(Request $request)
    {
        // Step 1: Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:addresses,address_id', // Assuming 'addresses' table contains the locations
            'additional_info' => 'nullable|string|max:255',
            'service_id' => 'required|exists:services,service_id',
            'service_date' => 'required|date', // Validate that the service_date is a valid date
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Step 2: Authenticate the user using the bearer token
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Step 3: Retrieve the location by its ID using the user's addresses relationship
        $location = $user->getUserAddress()->where('address_id', $request->input('location_id'))->first();

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }

        // Step 4: Create a new services_form record
        try {
            $servicesForm = new services_form();
            $servicesForm->user_id = $user->Users_id;
            $servicesForm->service_id = $request->input('service_id');
            $servicesForm->service_date = $request->input('service_date');
            $servicesForm->user_name = $user->user_name;
            $servicesForm->user_lastname = $user->user_lastname;
            $servicesForm->email = $user->email;
            $servicesForm->phone_number = $user->phone_number; // assuming this field exists in the user model
            $servicesForm->location = $location->name; // assuming the Address model has a 'name' attribute
            $servicesForm->additional_info = $request->input('additional_info');

            $servicesForm->save();

            return response()->json([
                'success' => true,
                'message' => 'Service form created successfully',
                'data' => $servicesForm
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function GetPoints($service_id, $user = null)
    {
        try {
            // Ensure the user is authenticated
            if (!$user) {
                $user = auth()->user();
            }
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Find the service by ID
            $service = services::find($service_id);
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found',
                ], 404);
            }

            // Calculate points based on service price
            $amount = $service->price;
            $points = $amount / 10;

            // Retrieve the user's points record and update it
            $userPoints = $user->getUserPoints()->first();
            if ($userPoints) {
                $userPoints->update([
                    'points' => $userPoints->points + $points,
                ]);
            } else {
                // If the user has no points record, create one
                $userPoints = user_points::create([
                    'user_id' => $user->Users_id,
                    'points' => $points,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Points updated successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    //function for storing the paymnet

    public function storePayment(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'service_id' => 'required|exists:services,service_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();


        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $service = services::find($request->service_id);
        $amount = $service->price;
        $name = $service->service_name;

        try {
            $payment = new payment();
            $payment->user_id = $user->Users_id;
            $payment->type = $request->input('type');
            $payment->service_name = $name;
            $payment->price = $amount;
            $payment->save();


            if ($request->type !== "yallacoin") {
                $pointsResponse = $this->GetPoints($request->service_id, $user);

                if ($pointsResponse->status() != 200) {
                    // Handle the case where points update failed
                    return $pointsResponse;
                }
            }


            return response()->json([
                'success' => true,
                'message' => 'Payment created successfully',
                'data' => $payment
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createPaymentIntent(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'service_id' => 'required|exists:services,service_id',
        ]);

        // Authenticate user
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Retrieve service details
        $service = services::find($request->service_id);
        $amount = $service->price * 100; // Amount in cents

        // Set Stripe secret key
        Stripe::setApiKey(config('stripe.secret'));

        // Create a PaymentIntent
        $paymentIntent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
            'metadata' => [
                'user_id' => $user->user_id,
                'service_id' => $service->service_id,
            ],
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    public function yallacoinPay(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'service_id' => 'required|exists:services,service_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $service = services::find($request->service_id);
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found',
                ], 404);
            }

            // Calculate points based on service price
            $amount = $service->price;

            $userPoints = $user->getUserPoints()->first();

            if (!$userPoints) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has no points record',
                ], 404);
            }

            $points = $userPoints->points;

            if ($points < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient points',
                ], 400);
            }

            $total = $points - $amount;

            // Update the user's points
            $userPoints->update([
                'points' => $total,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment created successfully',
                'amount' => $amount,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getUserPoints()
    {
        try {
            $user = auth()->user();

            $points = $user->getUserPoints;

            return response()->json($points, 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function storeOrder(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'form_id' => 'required|exists:services_forms,form_id',
            'payment_id' => 'required|exists:payments,payment_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {


            // Retrieve the ServiceForm by form_id
            $serviceForm = services_form::with('service')->find($request->input('form_id'));

            if (!$serviceForm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service form not found'
                ], 404);
            }

            // Get location name from ServiceForm
            $locationName = $serviceForm->location;

            // Find the address using the user's addresses and the location name
            $address = $user->getUserAddress()->where('name', $locationName)->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found'
                ], 404);
            }
            // Create the order
            $order = new orders([
                'user_id' => $user->Users_id,
                'payment_id' => $request->input('payment_id'),
                'form_id' => $request->input('form_id'),
                'status' => 'waiting',
            ]);

            $order->save();

            $order->load(['payments', 'service_forms']);

            $order->load(['service_forms.service']);

            // Extract service information
            $serviceInfo = $order->service_forms->services;

            $info =
                [
                    "address_info" => $address,
                    "service_info" => $serviceInfo,
                    "order_info" => $order
                ]

            ;



            $user->notify(new \App\Notifications\OrderNotification($info));

            // Retrieve additional details using relationships


            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                $info
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //->where('type', OrderNotification::class) if we want to get a specefic notification
    public function getUserNotification()
    {
        try {
            $user = auth()->user();

            $notifications = $user->notifications()->get();

            return response()->json($notifications, 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            // Validate the notification ID
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|uuid|exists:notifications,id',
            ]);

            // If the validation fails, return a 400 response with the validation errors
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            // Get the authenticated user
            $user = auth()->user();

            // Find the notification
            $notification = $user->notifications()->find($id);

            // If the notification exists, mark it as read
            if ($notification) {
                $notification->markAsRead();
            }

            // Return the notification in the response
            return response()->json($notification, 200);

        } catch (\Throwable $th) {
            // If an error occurs, return a 500 response with the error message
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function destroyUserNotification($id)
{
    try {
        // Validate the notification ID
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|uuid|exists:notifications,id',
        ]);

        // If validation fails, return a 400 response with the validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        // Get the authenticated user
        $user = auth()->user();

        // Find the notification
        $notification = $user->notifications()->find($id);

        // If the notification exists, delete it
        if ($notification) {
            $notification->delete();
            return response()->json([
                'status' => true,
                'message' => 'Notification deleted successfully',
            ], 200);
        }

        // If notification does not exist, return a 404 response
        return response()->json([
            'status' => false,
            'message' => 'Notification not found',
        ], 404);

    } catch (\Throwable $th) {
        // If an error occurs, return a 500 response with the error message
        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
        ], 500);
    }
}



    public function DeleteUserNotification()
    {
        try {
            $user = auth()->user();

            $notifications = $user->notifications();
            $notifications->delete();

            return response()->json("notifications deleted successfully !", 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function ReadAllUserNotification(){
        try {
            $user = auth()->user();
    
            // Retrieve only unread notifications
            $unreadNotifications = $user->notifications()->whereNull('read_at')->get();
    
            // Mark unread notifications as read
            foreach ($unreadNotifications as $notification) {
                $notification->markAsRead();
            }
    
            
            return response()->json("notifications marked as read !", 200);
    
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}