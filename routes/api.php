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

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
 */

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('payload', 'AuthController@payload');
    

});

Route::group(['middleware' => 'auth'], function () {
    Route::get('posts','DashboardController@posts');
    Route::get('dealers','DealerController@get');
    Route::get('allCSNumbers','CSNumberController@getCSNumbers');

    Route::post('coupon/submit','CouponController@store');
    Route::get('coupon/show/{couponId}','CouponController@show');
    Route::get('timeline/show/{couponId}','TimelineController@show');
    Route::get('denomination/show/{couponId}','DenominationController@show');
});

