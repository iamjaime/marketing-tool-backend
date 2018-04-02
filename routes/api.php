<?php

use Illuminate\Http\Request;
use App\Utils\Country;
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


    //The SMI Pool - UnAuthenticated Endpoint
    Route::get('smi/pool', 'OrderController@pool');
    // stripe
    Route::post('stripe/charge', 'PaymentController@chargeCard');
    Route::post('stripe/plans', 'PaymentController@plans');

    Route::post('stripe/webhook', 'PaymentController@webHook');

    Route::post('stripe/credits', 'PaymentController@getCredits');
    Route::post('stripe/cancelSubscription', 'PaymentController@cancelSubscription');
    Route::get('users/sub', 'UserController@sub');

    //Stats
    Route::get('smi-stats', 'StatController@index');


    //Reset Password
    Route::post('password/email', 'Auth\ForgotPasswordController@getResetToken');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::get('countries', function(){
       return Country::all();
    });

    //Require Authentication...
    Route::group(['middleware' => 'auth:api'], function(){

        //User Resource
        Route::get('users', 'UserController@index');
        //User Resource
        Route::get('users/sub', 'UserController@sub');
        Route::get('users/{id}', 'UserController@show');
      
        Route::put('users', 'UserController@update'); //Updates the user that is logged in.
        Route::delete('users/{id}', 'UserController@destroy');

        Route::post('users/attach_to_service_provider', 'UserController@attachUserToServiceProvider');

        //Order Resource
        Route::get('orders/service-provider/{id}', 'OrderController@providerOrders');

        //Handles getting all of the orders that a user can fill
        //This is for limited jobs....meaning that if an order was filled by this user within X amount of time, it returns only the orders
        //that are able to be filled at the current time EXCLUDING the orders that were already filled and need to wait for the waiting period to pass.
        Route::get('orders/service-provider/{id}/personal/{providerAccountId}', 'OrderController@personalProviderOrders');

        Route::get('orders/service-provider/{id}/owned', 'OrderController@ownedOrders');
        Route::post('orders', 'OrderController@store');

        //Handles filling the purchase order :)
        Route::post('orders/fill', 'OrderController@fill');

        //Companies Resource
        Route::resource('company', 'CompanyController', ['only' => ['show', 'store', 'update', 'destroy']]);

    });

});