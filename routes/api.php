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
Route::get('preview-coupon/{promo_id}', 'PdfController@previewCoupon');
Route::get('print-coupon/{coupon_id}/{email}', 'PdfController@printCoupon');
Route::get('approve/{approval_id}', 'ApprovalController@approve');
Route::get('reject/{approval_id}', 'ApprovalController@reject');
Route::post('reject', 'ApprovalController@rejectCoupon');
Route::get('print-voucher/{voucher_id}', 'PdfController@printVoucher');
Route::get('print-claim-request/{claim_request_id}', 'PdfController@printClaimRequest');

Route::get('promo/approve/{promo_id}/{approver_id}/{approver_source}','PromoController@approve');
Route::get('promo/reject/{promo_id}/{approver_id}/{approver_source}','PromoController@reject');
Route::post('promo/reject', 'PromoController@rejectPromo');

Route::get('voucher/claim/{voucher_code}', 'ClaimController@claimForm');
Route::post('voucher/claim', 'ClaimController@store');
Route::get('claim-request/approval/details/{approval_id}', 'ClaimRequestController@approval');
Route::get('claim-request/approve/{approval_id}', 'ClaimRequestController@approve');
Route::post('claim-request/reject-state', 'ClaimRequestController@reject');
Route::get('claim-request/reject/{approval_id}', 'ClaimRequestController@rejectForm');

// Instant approve
Route::get('instant-process/{coupon_id}', 'InstantController@instantProcess');

// OneSign pending
Route::get('approval/pending/{employee_number}', 'ApprovalController@getAllPending');

Route::get('approval/coupon/{approval_id}', 'ApprovalController@viewCoupon');
Route::get('approval/claim-request/{approval_id}', 'ApprovalController@viewClaimRequest');
Route::get('approval/promo/{promo_id}/{approver_user_id}/{approver_source_id}', 'ApprovalController@viewPromo');

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
    Route::post('claim-request/submit','ClaimRequestController@store');
    //Route::get('payments/get','ClaimRequestController@get');
    Route::get('claim-requests/get', 'ClaimRequestController@get');
    Route::get('claim-request/lines/get/{claimHeaderId}','ClaimRequestController@getLines');
    Route::get('claim-request/header/get/{claimHeaderId}','ClaimRequestController@getHeader');
    Route::get('claim-request/approvers/get/{claimHeaderId}','ClaimRequestController@approvers');
  
    Route::post('payment/update/status','ClaimRequestController@updateStatus');

    Route::get('dashboard/statistics','DashboardController@getStatistics');
   
    Route::get('coupon-types/get', 'CouponTypeController@index');

    Route::get('promos/active', 'PromoController@getActive');
    Route::get('promos/active/{coupon_type_id}', 'PromoController@getActiveByCouponType');
    Route::get('promos','PromoController@index');
    Route::get('promos/{coupon_type_id}','PromoController@getByCouponType');
    Route::post('promo/create','PromoController@store');
    Route::patch('promo/update','PromoController@update');
    Route::patch('promo/cancel','PromoController@cancel');
   
    Route::get('purpose/active', 'PurposeController@getActive');
    Route::get('purpose', 'PurposeController@get');
    Route::post('purpose/create', 'PurposeController@store');
    Route::post('purpose/update', 'PurposeController@update');

    Route::get('approval/coupon/get/{coupon_id}', 'ApprovalController@getByCoupon');
    Route::post('approval/resend', 'ApprovalController@resend');
    Route::post('coupon/resend', 'CouponController@resend');

    Route::get('claims/get', 'ClaimController@get');
    Route::post('claim-request/cancel', 'ClaimRequestController@cancel');
    
    
    // Reports
    Route::get('report/voucher-summary', 'ReportController@getVoucherSummary');


    // users
    Route::get('user/dealer', 'UserController@getUserDealer');
    
    
});


