<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/login', [UserController::class, 'loginUser']);

Route::group([
    "middleware" => ["auth:sanctum"]
],function(){

    Route::get("profile",[UserController::class, 'profile']);
    Route::get("logout",[UserController::class, 'logout']);
    Route::get("UserLocations",[UserController::class, 'getUserLocations']);


    Route::put("/profile/updateUser",[UserController::class, 'updateUser']);
    Route::put("/updatePassword",[UserController::class, 'updatePassword']);
    Route::put('/UpdateUserLocation/{location_id}', [UserController::class, 'UpdateUserLocation']);

    Route::post('createUserLocation', [UserController::class, 'CreateUserLocation']);



});

Route::get('/getAllServices', [UserController::class, 'getAllServices']);


