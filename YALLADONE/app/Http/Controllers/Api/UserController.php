<?php

namespace App\Http\Controllers\Api;

use App\Models\address;
use App\Models\users;
use App\Models\services;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
                'phone_number'=>'required',
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
            'phone_number'=>'required',
            'email' => 'required|email|'.$user->id,
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

        return response()->json([
            'status' => true,
            'message' => 'User locations retrieved successfully',
            'data' => $locations,
        ], 200);

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





}
