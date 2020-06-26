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
Route::get('print-coupon/{coupon_id}/{email}', 'PdfController@printCoupon');
Route::get('approve/{approval_id}', 'ApprovalController@approve');
Route::get('reject/{approval_id}', 'ApprovalController@reject');
Route::post('reject', 'ApprovalController@rejectCoupon');

 // Download Route
Route::get('download/voucher-template', function(){
 
    // Check if file exists in app/storage/file folder
    $filename = 'voucher_template.xlsx';
    $file_path = storage_path() .'/app/public/' . $filename;
    if (file_exists($file_path))
    {
        // Send Download
        return Response::download($file_path, $filename, [
            'Content-Length: '. filesize($file_path)
        ]);
    }
    else
    {
        // Error
        exit('Requested file does not exist on our server!');
    }
});


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

Route::group(['middleware' => 'jwt'], function () {

    Route::get('approval/get/','ApprovalController@get');

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
    Route::post('coupon/receive/fleet','CouponController@receiveFleet');
    Route::post('coupon/receive/dealer','CouponController@receiveDealer');

    // Documents
    Route::post('coupon/generate/','VoucherController@generate');
    
    Route::get('timeline/show/{couponId}','TimelineController@show');
    Route::get('denomination/show/{couponId}','DenominationController@show');
    
    // Payment
    Route::get('voucher/get/{couponId}','VoucherController@show');
    Route::post('payment-request/submit','PaymentRequestController@store');
    Route::get('payments/get','PaymentRequestController@get');
    Route::get('payment/lines/get/{paymentHeaderId}','PaymentRequestController@getLines');
    Route::get('payment/header/get/{paymentHeaderId}','PaymentRequestController@getHeader');
    Route::post('payment/update/status','PaymentRequestController@updateStatus');

    Route::get('dashboard/statistics','DashboardController@getStatistics');
   
    Route::get('coupon-types/get', 'CouponTypeController@index');

    Route::get('promos/active', 'PromoController@getActive');
    Route::get('promos','PromoController@index');
    Route::post('promo/create','PromoController@store');
    Route::post('promo/update','PromoController@update');

    Route::get('purpose/active', 'PurposeController@getActive');
    Route::get('purpose', 'PurposeController@get');
    Route::post('purpose/create', 'PurposeController@store');

    Route::get('approval/coupon/get/{coupon_id}', 'ApprovalController@getByCoupon');
    Route::post('approval/resend', 'ApprovalController@resend');
    Route::post('coupon/resend', 'CouponController@resend');
    

});


