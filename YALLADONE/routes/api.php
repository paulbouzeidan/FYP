<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LoginApiContoller;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('store', [LoginController::class, 'store']);
Route::resource('user',LoginController::class);

Route::resource('login',LoginApiContoller::class);
Route::post('login/store', [LoginApiContoller::class,'store']);

// Route::post('/ValidateLogin', [LoginApiContoller::class, 'login']);
//route for validating user login 
// Route::middleware('auth')->group(function () {
 
Route::post('/ValidateLogin', [LoginApiContoller::class, 'login']);



// });
Route::middleware('auth')->group(function () {
   
});
//return all the services in the db 
Route::get('/getAllServices', [LoginApiContoller::class, 'getAllServices'])->name('getAllServices');


// Route::put('/changePassword', [LoginApiContoller::class, 'changePassword'])->name('changePassword');
Route::put('/changepassword', [LoginApiContoller::class, 'changepassword']);

Route::put('/changeUserInfo', [LoginApiContoller::class, 'changeUserInfo']);