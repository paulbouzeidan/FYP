<?php

namespace App\Http\Controllers\Api;

use App\Models\address;
use App\Models\Otp;
use App\Models\users;
use App\Models\services;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\TestMail;
use App\Models\user_points;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\YallaDoneMail;
use App\Models\FavService;

use App\Notifications\SignupNotification;

class UserController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return users
     */
    public function createUser(Request $request)
    {


        try {
            //Validated
            $validateUser = Validator::make($request->all(),
            [
                'user_name' => 'required',
                'user_lastname'=>'required',
                'birthday'=>'required',
                'phone_number' => 'required|unique:users,phone_number',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = users::create([
                'user_name' => $request->user_name,
                'user_lastname' => $request->user_lastname,
                'birthday' => $request->birthday,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);



            $points = user_points::create([
                'user_id' => $user->Users_id,
                'points' => "0",

            ]);

            $info=$user;
            
        $user->notify(new \App\Notifications\SignupNotification($info));



            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function generateOtp(Request $request)
    {
        try {
            // Get authenticated user
            $user = auth()->user();
            $id = $user->Users_id;
            $email = $user->email;

            // Check for existing OTP and delete it if found
            Otp::where('user_id', $id)->delete();

            // Generate a new OTP
            $otp = mt_rand(100000, 999999);

            // Set OTP expiration time (e.g., 1 minute)
            $expiresAt = now()->addMinutes(1);

            // Store the new OTP in the database
            Otp::create([
                'user_id' => $id,
                'email' => $email,
                'otp' => $otp,
                'expires_at' => $expiresAt,
            ]);

            // Send OTP via email
            Mail::raw("Your OTP is: $otp", function($message) use ($user) {
                $message->to($user->email)
                        ->subject('OTP Verification');
            });

            return response()->json([
                'status' => true,
                'message' => 'OTP generated and sent successfully',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function verifyOtp(Request $request, $otp)
    {
        try {
            // Get authenticated user
            $user = auth()->user();

            // Fetch the OTP record
            $otpRecord = Otp::where('email', $user->email)
                            ->where('otp', $otp)
                            ->first();

            // Check if OTP record exists
            if (!$otpRecord) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP is invalid'
                ], 401);
            }

            // Check if OTP has expired
            if ($otpRecord->expires_at->isPast()) {
                // Delete the user
                $user->delete();

                // Delete the OTP record
                $otpRecord->delete();

                return response()->json([
                    'status' => false,
                    'message' => 'OTP has expired. User account deleted.'
                ], 401);
            }

            // Update user's verification status
            $user->update([
                'is_verified' => now()
            ]);

            // Delete the OTP record
            $otpRecord->delete();


            $mailData = $user->user_name;
                $email =$user->email;
                // Send the email
                Mail::to($email)->send(new YallaDoneMail($mailData));

            return response()->json([
                'status' => true,
                'message' => 'User verified successfully'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function destroyUnverifiedUser()
    {
        try {
            // Delete users where is_verified is null
            $deletedUsers = users::whereNull('is_verified')->delete();

            return response()->json([
                'status' => true,
                'message' => 'Unverified users deleted successfully',
                'deleted_count' => $deletedUsers,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return users
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'identifier' => 'required',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $credentials = $request->only('identifier', 'password');

            // Check if the identifier is an email or a phone number
            $identifier = $credentials['identifier'];
            $user = users::where(function ($query) use ($identifier) {
                $query->where('email', $identifier)
                    ->orWhere('phone_number', $identifier);
            })->first();

            if (!$user || !Auth::attempt(['email' => $user->email, 'password' => $credentials['password']])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email/Phone Number & Password do not match with our records.',
                ], 401);
            }

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function profile(){
      $userData=auth()->user();
      return response()->json([
        'status' => true,
        'message' => 'Profile info',
        'data' =>  $userData,
        'id' => auth()->user()->Users_id,
    ], 200);

    }

    public function updateUser(Request $request)
{
    try {
        $user = auth()->user();

        //Validated
        $validateUser = Validator::make($request->all(), [
            'user_name' => 'required',
            'user_lastname'=>'required',
            'birthday'=>'required',
            'phone_number' => 'required',
            'email' => 'required|email' ,
        ]);

        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        $user->update([
            'user_name' => $request->user_name,
            'user_lastname' => $request->user_lastname,
            'birthday' => $request->birthday,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User Updated Successfully',
            'data' => $user
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}


public function updatePassword(Request $request)
{
    try {
        $user = auth()->user();

        //Validated
        $validatePassword = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8',
        ]);

        if($validatePassword->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validatePassword->errors()
            ], 401);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Old password is incorrect'
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password Updated Successfully',
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}





    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'User Logged out ',
            'data' => [],

        ], 200);
    }

    public function DestroyUser(Request $request)
{
    try {
        // Retrieve the authenticated user
        $user = auth()->user();

        $userid=$user->Users_id;

        if ( $userid) {

            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'user deleted successfully'
            ], 200);
        } else {

            return response()->json([
                'status' => false,
                'message' => 'user not found !'
            ], 404);
        }
    } catch (\Throwable $th) {

        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}
    public function getAllServices()
    {
        // Fetch all services from the database
        $services = services::all();

        // Return the services as a JSON response
        return response()->json($services, 200);
        // return ['services' => $services];

    }

    //address functions

    public function getUserLocations()
{
    try {
        $user = auth()->user();

        $locations = $user->getUserAddress;

        return response()->json($locations, 200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}




public function CreateUserLocation(Request $request)
{
    try {
        $user = auth()->user();
        $ip = auth()->user()->Users_id;


        // Validation rules for location data
        $validateLocation = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',
            'location_type' => 'required|string',
            'name' => 'required|string',
            'district' => 'required|string',
            'city' => 'required|string',
            'street' => 'required|string',
            'building' => 'required|string',
            'floor' => 'required|string',
            'additional_info' => 'nullable|string',
        ]);

        if($validateLocation->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateLocation->errors()
            ], 401);
        }

        // Create new address record
        $address = address::create([
            'user_id' => $ip,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'location_type' => $request->location_type,
            'name' => $request->name,
            'district' => $request->district,
            'city' => $request->city,
            'street' => $request->street,
            'building' => $request->building,
            'floor' => $request->floor,
            'additional_info' => $request->additional_info,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Location created successfully',
            'data' => $address
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}


public function UpdateUserLocation(Request $request, $location_id)
{

    try {
        $user = auth()->user();
        // Find the location by ID
        $location = address::where('address_id', $location_id)->where('user_id', $user->Users_id)->firstOrFail();



        // Validation rules for location data
        $validateLocation = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',
            'location_type' => 'required|string',
            'name' => 'required|string',
            'district' => 'required|string',
            'city' => 'required|string',
            'street' => 'required|string',
            'building' => 'required|string',
            'floor' => 'required|string',
            'additional_info' => 'nullable|string',
        ]);

        if($validateLocation->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateLocation->errors()
            ], 401);
        }

        // Update location data
        $location->update([
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'location_type' => $request->location_type,
            'name' => $request->name,
            'district' => $request->district,
            'city' => $request->city,
            'street' => $request->street,
            'building' => $request->building,
            'floor' => $request->floor,
            'additional_info' => $request->additional_info,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Location updated successfully',
            'data' => $location
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}


public function DestroyUserLocation(Request $request, $id)
{
    try {
        // Retrieve the authenticated user
        $user = auth()->user();

        // Find the location by ID and ensure it belongs to the authenticated user
        $location = $user->getUserAddress()->find($id);

        if ($location) {
            // Delete the location
            $location->delete();

            return response()->json([
                'status' => true,
                'message' => 'Location deleted successfully'
            ], 200);
        } else {
            // Location not found or does not belong to the user
            return response()->json([
                'status' => false,
                'message' => 'Location not found or does not belong to the user'
            ], 404);
        }
    } catch (\Throwable $th) {
        // Catch any other exceptions and return a server error response
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}

public function getOrderHistory()
{
    try {
        $user = auth()->user();

        $orderHistory = $user->getUserOrders()->with(['payment', 'service_form','service'])->get();


        return response()->json($orderHistory, 200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}


public function addFavService(Request $request)
{
    try {
        // Validate the incoming request data
        $request->validate([
            'service_id' => 'required|exists:services,service_id',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Find the existing FavService
        $existingFavService = FavService::where('user_id', $user->Users_id)
            ->where('service_idF', $request->service_id)
            ->first();

        // Check if the existing FavService exists
        if ($existingFavService) {
            // Toggle the IsFav status
            $existingFavService->update(['IsFav' => !$existingFavService->IsFav]);

            return response()->json([
                'status' => 'success',
                'message' => 'Favorite service updated successfully',
                'data' => $existingFavService
            ], 200);
        } else {
            // If it doesn't exist, create a new fav service
            $favService = FavService::create([
                'user_id' => $user->Users_id,
                'service_idF' => $request->service_id,
                'IsFav' => true
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Favorite service created successfully',
                'data' => $favService
            ], 201);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while processing your request',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function getFavService(){
    try {

        $user = auth()->user();


        $favServices = $user->favServices()->where('IsFav', true)->with('service')->get();

        return response()->json($favServices, 200);

    } catch (\Throwable $th) {
        // If an error occurs, return a 500 response with the error message
        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
        ], 500);
    }
}

public function EmergencyService()
{
    try {
        // Retrieve emergency services
        $emergencyServices = services::where('isEmergency', true)->get();

        return response()->json(

           $emergencyServices
        , 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while processing your request',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function getAllServicesWithFavorites()
{
    try {
        // Get the authenticated user
        $user = auth()->user();

        // Retrieve the user's favorite services where IsFav is true, with the related service details
        $favServices = $user->favServices()->where('IsFav', true)->with('service')->get();

        // Get the IDs of the favorite services
        $favServiceIds = $favServices->pluck('service_idF')->toArray();

        // Retrieve all services
        $allServices = services::all();

        // Add an additional attribute to each service indicating its favorite status
        $allServicesWithFavorites = $allServices->map(function ($service) use ($favServiceIds) {
            // Check if the service ID exists in the list of favorite service IDs
            $isFavorite = in_array($service->service_id, $favServiceIds);
            // Add the 'isFavorite' attribute to the service object
            $service->isFavorite = $isFavorite;
            return $service;
        });

        return response()->json([
            'status' => 'success',
            'data' => $allServicesWithFavorites
        ], 200);

    } catch (\Throwable $th) {
        // If an error occurs, return a 500 response with the error message
        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
        ], 500);
    }
}


}
