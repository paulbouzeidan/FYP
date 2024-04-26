<?php

namespace App\Http\Controllers;

use App\Models\users;
use Illuminate\Http\Request;
use App\Models\user_points;
class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $obj=users::find(1);

        $points=$obj->getUserPoints;

       $arr= [
            'customer:'=>$obj,
            "points:"=>$points
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
        $obj = new users();
        $obj->user_name = $request->user_name;
        $obj->user_lastname = $request->user_lastname;
        $obj->email = $request->email;
        $obj->age = $request->age;
        $obj->phone_number = $request->phone_number;
        $obj->password = $encryptedPassword; // Save encrypted password
        $obj->save();
    
        return "Welcome $obj->user_name $obj->user_lastname";
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Retrieve the user data based on the provided ID
        $users = users::findOrFail($id);
        
        // Return the personDescription view with the retrieved user data
        return view('userDescription', ['user' => $users]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data= users::find($id);
        return view('edituser')->with('user',$data);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $obj= users::find($id);

        $obj->user_name = $request->user_name;
        $obj->user_lastname = $request->user_lastname;
        $obj->email = $request->email;
        $obj->age = $request->age;
        $obj->phone_number = $request->phone_number;
        $obj->password = $request->password; 
        $obj->save();

        return redirect()->route('');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $obj = users::find($id);
       $obj->delete(); 
       return redirect()->route('');
    }
}
