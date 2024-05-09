<?php

namespace App\Http\Controllers;

use App\Models\users;
use Illuminate\Http\Request;
use App\Models\user_points;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\services;

class LoginApiContoller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        header('Access-Control-Allow-Origin: *');

        $obj = users::find(1);

        $points = $obj->getUserPoints;

        $arr = [
            'customer:' => $obj,
            "points:" => $points
        ];

        return $arr;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'user_lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'age' => 'nullable|integer|min:18',
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number',
            'password' => 'required|string|min:8',

        ]);

        // Encrypt the password
        $encryptedPassword = bcrypt($request->password);

        // Proceed with saving the record
        $user = new users();
        $user->user_name = $request->user_name;
        $user->user_lastname = $request->user_lastname;
        $user->email = $request->email;
        $user->age = $request->age;
        $user->phone_number = $request->phone_number;
        $user->password = $encryptedPassword; // Save encrypted password
        $user->save();

        return response()->json([
            'message' => "Welcome $user->user_name $user->user_lastname",
            'user' => $user,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }




    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $credentials = $request->only('identifier', 'password');

        // Attempt to find the user by email
        $user = users::where('email', $credentials['identifier'])->first();

        // If user not found by email, attempt to find by phone number
        if (!$user) {
            $user = users::where('phone_number', $credentials['identifier'])->first();
        }

        // If user is found, attempt login
        if ($user && Auth::attempt(['email' => $user->email, 'password' => $credentials['password']])) {
            $token = $user->createToken('MyApp')->plainTextToken;
            return response()->json(['user' => $user, 'access_token' => $token]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
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

}