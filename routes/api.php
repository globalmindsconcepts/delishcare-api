<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix'=>'auth','namespace'=>'App\Http\Controllers'], function() {
    Route::post('login','AuthController@login');
    Route::post('register','AuthController@register');
    Route::post('email-confirmation','AuthController@emailConfirmation');//resendEmailConfirmationCode
    Route::post('resend-email-confirmation','AuthController@resendEmailConfirmationCode');

    Route::post('reset-password-link','AuthController@sendPasswordResetEmail');
    Route::post('reset-password','AuthController@resetPassword');

    Route::post('change-password-link','AuthController@sendPasswordChangeEmail');
    Route::post('change-password','AuthController@resetPassword');

    Route::post('admin-login','AuthController@adminLogin');
    Route::get('user','AuthController@authUser')->middleware('auth:sanctum');
    Route::get('admin','AuthController@authAdmin')->middleware('auth:sanctum');
});

Route::group(['prefix'=>'users','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::get('{user_uuid}/profile','ProfileController@show');
    Route::post('{user_uuid}/create-profile','ProfileController@store');
    Route::post('{user_uuid}/update-profile','ProfileController@update');
    Route::get('{user_uuid}/update-bank','ProfileController@updateBank');
    //Route::post('{user_uuid}/initiate-payment','AuthController@login');
});

Route::group(['prefix'=>'bonuses','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::get('{user_uuid}/welcome-bonus','WalletController@welcomeBonus');
    Route::get('{user_uuid}/profit-pool','WalletController@profitPool');
    Route::get('{user_uuid}/profit-pools','WalletController@profitPools');
    Route::get('{user_uuid}/equilibrum-bonus','WalletController@equilibrumBonus');
    Route::get('{user_uuid}/loyalty-bonus','WalletController@loyaltyBonus');
    Route::get('{user_uuid}/referral-bonus','WalletController@referralBonus');
    Route::get('{user_uuid}/placement-bonus','WalletController@placementBonus');
    Route::get('{user_uuid}/total-bonus','WalletController@totalBonus');
    Route::get('{user_uuid}/global-profit','WalletController@globalProfit');
});

Route::group(['prefix'=>'incentives','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::post('create','IncentiveController@store');
    Route::post('update','IncentiveController@update');
    Route::get('{id}','IncentiveController@show');
    Route::delete('{id}','IncentiveController@delete');
    Route::get('all','IncentiveController@index');
});

Route::group(['prefix'=>'packages','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::post('create','PackageController@create');
    Route::put('update','PackageController@update');
    Route::get('{id}','PackageController@show');
    Route::delete('{id}','PackageController@destroy');
    Route::get('all','PackageController@index');
});

Route::group(['prefix'=>'ranks','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::post('create','RankController@create');
    Route::put('update','RankController@update');
    Route::get('{id}','RankController@show');
    Route::delete('{id}','RankController@destroy');
    Route::get('all','PackageController@all');
});

Route::group(['prefix'=>'settings','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::put('update','SettingController@update');
    Route::put('/update-referral-bonus/{id}','SettingController@updateReferralBonusSetting');
});

Route::group(['prefix'=>'payments','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::post('initiate','PaymentController@initiatePackagePayment');
    Route::post('verify','PaymentController@verifyPackagePayment');
});

Route::group(['middleware' => ['auth:sanctum', 'admin.auth'], 'namespace' => 'App\Http\Controllers'], function () {
    Route::group(['prefix' => 'product-services'], function () {
        Route::get('all', 'ProductServiceController@all');
        Route::post('create', 'ProductServiceController@store');
        Route::put('{id}/update', 'ProductServiceController@update');
        Route::get('{id}', 'ProductServiceController@show');
        Route::delete('{id}', 'ProductServiceController@destroy');
    });

    Route::group(['prefix' => 'service-providers'], function () {
        Route::get('all', 'ServiceProviderController@all');
        Route::post('create', 'ServiceProviderController@store');
        Route::put('{id}/update', 'ServiceProviderController@update');
        Route::get('{id}', 'ServiceProviderController@show');
        Route::delete('{id}', 'ServiceProviderController@destroy');
    });
});
