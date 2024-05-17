<?php

namespace App\Http\Controllers\Api;

use App\Models\users;
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

    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'User Logged out ',
            'data' => [],
           
        ], 200);
    }

   
}