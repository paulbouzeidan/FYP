<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\sendEmail;
use App\Http\Controllers\UserServiceForm;


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

Route::get('/getAllServices', [UserController::class, 'getAllServices']);
Route::get('/getNews', [NewsController::class, 'getNews']);

Route::delete('/unverified-users', [UserController::class, 'destroyUnverifiedUser']);

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

    Route::post('/send',[sendEmail::class,'send'])->middleware('auth');

    Route::post('/YallaDoneSend',[sendEmail::class,'YallaDoneSend']);

    Route::delete('/DestroyUserLocation/{id}', [UserController::class, 'DestroyUserLocation']);

    Route::delete('/DestroyUser', [UserController::class, 'DestroyUser']);


    Route::post('/generate-otp', [UserController::class, 'generateOtp']);

    Route::post('/verify-otp/{otp}', [UserController::class, 'verifyOtp']);


    Route::post('StoreUserServiceForm', [UserServiceForm::class, 'StoreUserServiceForm']);

    Route::post('storePayment', [UserServiceForm::class, 'storePayment']);

    Route::post('createPaymentIntent', [UserServiceForm::class, 'createPaymentIntent']);
    
    Route::post('storeOrder', [UserServiceForm::class, 'storeOrder']);




});




