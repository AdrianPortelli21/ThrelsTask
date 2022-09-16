<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CodeCheckController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(AuthController::class)->group(function(){

    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('logout', 'logout');
    Route::get('getloggedinuser', 'getAuthUser');

});


Route::controller(EventsController::class)->group(function(){
    Route::post('event','store');
    Route::delete('event/{id}','destroy');
    Route::put('event/{id}','update');
});

Route::post('password/email',  ForgotPasswordController::class);
Route::post('password/code/check', CodeCheckController::class);
Route::post('password/reset', ResetPasswordController::class);
