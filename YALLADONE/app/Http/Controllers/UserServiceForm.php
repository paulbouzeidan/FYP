<?php

namespace App\Http\Controllers;

use App\Models\services_form;
use App\Models\payment;
use App\Models\orders;
use Illuminate\Support\Facades\Auth;
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
        $user =auth()->user();

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

    $user =auth()->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }

    $service = services::find($request->service_id);
    $amount = $service->price ;
    $name=$service->service_name;
    try {
        $payment = new payment();
        $payment->user_id = $user->Users_id;
        $payment->type = $request->input('type');
        $payment->service_name =  $name;
        $payment->price =  $amount;
        $payment->save();

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
            // Create the order
            $order = new orders([
                'user_id' => $user->Users_id,
                'payment_id' => $request->input('payment_id'),
                'form_id' => $request->input('form_id'),
                'pending' => true
            ]);

            $order->save();

            // Retrieve additional details using relationships


            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',

            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
