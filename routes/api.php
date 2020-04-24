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
 
Route::get('print-coupon/{coupon_id}', 'PdfController@printCoupon');


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
    
    // Coupon
    Route::post('coupon/submit','CouponController@store');
    Route::get('coupon/show/{couponId}','CouponController@show');
    Route::get('coupon/get/','CouponController@get');
    Route::post('coupon/approve/','ApprovalController@approve');
    Route::post('coupon/reject/','ApprovalController@reject');
    Route::post('coupon/update/','CouponController@update');
    Route::post('coupon/issue/','CouponController@issue');
    Route::post('coupon/receive-fleet/','CouponController@receiveFleet');
    Route::post('coupon/receive-dealer/','CouponController@receiveDealer');

    // Documents
    Route::post('coupon/generate/','CouponDocsController@generate');

    
    Route::get('timeline/show/{couponId}','TimelineController@show');
    Route::get('denomination/show/{couponId}','DenominationController@show');
    
    Route::get('approval/get/','ApprovalController@get');
    
});


