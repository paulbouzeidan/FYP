<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\sendEmail;
use App\Http\Controllers\UserServiceForm;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\SMSController;

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
Route::get('/send-sms', [SMSController::class, 'sendSMS']);

Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/login', [UserController::class, 'loginUser']);

Route::get('/getAllServices', [UserController::class, 'getAllServices']);
Route::get('/getNews', [NewsController::class, 'getNews']);
Route::get('/EmergencyService', [UserController::class, 'EmergencyService']);

Route::delete('/unverified-users', [UserController::class, 'destroyUnverifiedUser']);

Route::post('/news', [NewsController::class, 'storeNews']);
Route::post('/storeService', [NewsController::class, 'storeService']);


Route::get('/car-services', [ServiceCategoryController::class, 'getCarServices']);
Route::get('/transportation-services', [ServiceCategoryController::class, 'getTransportationServices']);
Route::get('/paperwork-services', [ServiceCategoryController::class, 'getPaperworkServices']);
Route::get('/delivery-services', [ServiceCategoryController::class, 'getDeliveryServices']);

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

    Route::post('yallacoinPay', [UserServiceForm::class, 'yallacoinPay']);



    Route::get('getUserPoints', [UserServiceForm::class, 'getUserPoints']);

    Route::get('getOrderHistory', [UserController::class, 'getOrderHistory']);
    Route::get('getFavService', [UserController::class, 'getFavService']);


    Route::post('addFavService', [UserController::class, 'addFavService']);

    Route::get('getUserNotification', [UserServiceForm::class, 'getUserNotification']);

    Route::get('markAsRead/{id}', [UserServiceForm::class, 'markAsRead']);

    Route::get('DestroyUserNotifiation/{id}', [UserServiceForm::class, 'DestroyUserNotifiation']);

    Route::get('getAllServicesWithFavorites', [UserController::class, 'getAllServicesWithFavorites']);




});




