<?php

use Illuminate\Http\Request;

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


//Version 1 api route group
Route::group(['prefix' => 'v1'], function () {

    //User Registration - UnAuthenticated Endpoint
    Route::post('users', 'UserController@store');

    //Reset Password
    Route::post('password/email', 'Auth\ForgotPasswordController@getResetToken');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');


    //Require Authentication...
    Route::group(['middleware' => 'auth:api'], function(){

        //User Resource
        Route::get('users', 'UserController@index');
        Route::get('users/{id}', 'UserController@show');
        Route::put('users', 'UserController@update'); //Updates the user that is logged in.
        Route::delete('users/{id}', 'UserController@destroy');

        Route::post('users/attach_to_service_provider', 'UserController@attachUserToServiceProvider');


        //Order Resource
        Route::get('orders/service-provider/{id}', 'OrderController@providerOrders');
        Route::get('orders/service-provider/{id}/owned', 'OrderController@ownedOrders');
        Route::post('orders', 'OrderController@store');

        //Handles filling the purchase order :)
        Route::post('orders/fill', 'OrderController@fill');


    });

});