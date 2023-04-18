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
    Route::get('admin','AuthController@authAdmin')->middleware(['auth:sanctum','admin.auth']);
    Route::put('{uuid}/change-user-password','AuthController@changeUserPassword')->middleware(['auth:sanctum','admin.auth']);
    Route::put('toggle-admin-2fa','AuthController@toggleAdmin2fa')->middleware(['auth:sanctum','admin.auth']);
    Route::post('authenticate-2fa','AuthController@authenticate2FA');
    Route::post('authenticate-2fa','AuthController@authenticate2FA');
    Route::post('send-2fa-verification-email','AuthController@send2faEmail');
});

Route::group(['prefix'=>'users','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::get('/','UserController@users');
    Route::get('{user_uuid}/profile','ProfileController@show');
    Route::post('{user_uuid}/create-profile','ProfileController@store');
    Route::post('{user_uuid}/update-profile','ProfileController@update');
    Route::put('{user_uuid}/update','UserController@update');
    Route::put('{user_uuid}/update-bank-details','ProfileController@updateBankDetails');
    Route::get('{uuid}/total-pv','UserController@totalPointValues');
    Route::get('{uuid}/upline-details','UserController@uplineDetails');
    Route::get('{uuid}/downlines','UserController@downlines');
    Route::get('{uuid}/direct-downlines','UserController@directDownlines');
    Route::get('{uuid}/genealogy','UserController@genealogy');
    Route::post('{uuid}/invite-guest','UserController@inviteGuest');
    Route::get('total-registrations','UserController@totalRegistrations')->middleware('admin.auth');
    Route::get('total-registration-pv','UserController@totalRegistrationPV')->middleware('admin.auth');
    Route::get('{uuid}/user','UserController@getUser')->middleware('admin.auth');
    Route::post('{uuid}/send-message','UserController@sendMessage')->middleware('admin.auth');
    Route::put('{uuid}/toggle-2fa','ProfileController@toggle2FA');//->middleware('admin.auth');
    Route::put('{uuid}/bank-editable','ProfileController@setBankEditable')->middleware('admin.auth');
    Route::get('paid-users','UserController@paidUsers')->middleware('admin.auth');
    Route::get('total-paid-users','UserController@totalPaidUsers')->middleware('admin.auth');
    Route::get('sum-paid-users','UserController@sumPaidUsers')->middleware('admin.auth');
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
    Route::get('{user_uuid}/global-profits','WalletController@globalProfits');
    Route::get('{user_uuid}/wallet-balance','WalletController@totalBalance');
    Route::get('total-equilibrum-bonus','WalletController@totalEquilibrumBonus');
    Route::get('total-loyalty-bonus','WalletController@totalLoyaltyBonus');
    Route::get('total-profit-pool-bonus','WalletController@totalProfitPoolBonus');
    Route::get('total-global-profit-bonus','WalletController@totalGlobalProfitBonus');
    Route::get('company-wallet-balance','WalletController@companyWalletBalance');
    Route::get('total-company-wallet','WalletController@totalCompanyWallet');
});

Route::group(['prefix' => 'incentives', 'middleware' => ['auth:sanctum'], 'namespace' => 'App\Http\Controllers'], function () {
    Route::get('all','IncentiveController@index');
    Route::post('create','IncentiveController@store');
    Route::post('{id}/update','IncentiveController@update');
    Route::get('{id}','IncentiveController@show');
    Route::delete('{id}','IncentiveController@delete');
});

Route::group(['prefix' => 'incentive-claims', 'middleware' => ['auth:sanctum'], 'namespace' => 'App\Http\Controllers'], function () { 
    Route::get('all','IncentiveClaimController@all');
    Route::post('{uuid}/create','IncentiveClaimController@create');
    Route::put('{id}/approve','IncentiveClaimController@approve');
    Route::put('{id}/decline','IncentiveClaimController@decline');
    Route::get('{uuid}/claims','IncentiveClaimController@claimedIncentives');//not tested
    Route::get('{uuid}/current-incentive','IncentiveClaimController@currentIncentive');//not tested
});

Route::group(['prefix'=>'packages', 'namespace'=>'App\Http\Controllers'], function() {
    Route::post('create','PackageController@store')->middleware(['auth:sanctum','admin.auth']);
    Route::put('{id}/update','PackageController@update')->middleware(['auth:sanctum','admin.auth']);
    Route::get('all','PackageController@index');//->middleware(['admin.auth','auth:sanctum']);
    Route::get('{id}','PackageController@show');
    Route::delete('{id}','PackageController@destroy')->middleware(['auth:sanctum','admin.auth']);
});

Route::group(['prefix'=>'ranks','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::get('all','RankController@index')->middleware('admin.auth');
    Route::post('create','RankController@store')->middleware('admin.auth');
    Route::put('{id}/update','RankController@update')->middleware('admin.auth');
    Route::get('{id}','RankController@show');
    Route::delete('{id}','RankController@destroy')->middleware('admin.auth');
});

Route::group(['prefix'=>'settings','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::get('all','SettingController@getSettings');
    Route::get('referral-bonus','SettingController@getReferralBonusSetting');
    Route::put('update','SettingController@update');
    Route::put('/update-referral-bonus/{id}','SettingController@updateReferralBonusSetting');
    Route::get('/{column}','SettingController@show');
});

Route::group(['prefix'=>'payments','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::post('initiate','PaymentController@initiatePackagePayment');
    Route::post('verify','PaymentController@verifyPackagePayment');
});

Route::group(['middleware' => ['auth:sanctum'], 'namespace' => 'App\Http\Controllers'], function () {
    Route::group(['prefix' => 'product-services'], function () {
        Route::get('all', 'ProductServiceController@index');
        Route::post('create', 'ProductServiceController@store')->middleware('admin.auth');
        Route::put('{id}/update', 'ProductServiceController@update')->middleware('admin.auth');
        Route::get('{id}', 'ProductServiceController@show');
        Route::delete('{id}', 'ProductServiceController@destroy')->middleware('admin.auth');
    });

    Route::group(['prefix' => 'service-providers'], function () {
        Route::get('all', 'ServiceProviderController@index');
        Route::post('create', 'ServiceProviderController@store')->middleware('admin.auth');
        Route::put('{id}/update', 'ServiceProviderController@update')->middleware('admin.auth');
        Route::get('{id}', 'ServiceProviderController@show');
        Route::delete('{id}', 'ServiceProviderController@destroy')->middleware('admin.auth');
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('all', 'ProductController@index');
        Route::post('create', 'ProductController@store')->middleware('admin.auth');
        Route::put('{id}/update', 'ProductController@update')->middleware('admin.auth');
        Route::get('{id}', 'ProductController@show');
        Route::delete('{id}', 'ProductController@destroy')->middleware('admin.auth');
        Route::post('{uuid}/select', 'ProductController@selectProduct');
    });

    Route::group(['prefix' => 'product-claims'], function () {
        Route::get('all','ProductClaimController@all');
        Route::post('{uuid}/create','ProductClaimController@create');
        Route::put('{uuid}/approve','ProductClaimController@approve');
        Route::put('{uuid}/decline','ProductClaimController@decline');
        Route::get('{uuid}/claims','ProductClaimController@claimedProducts');//not tested
        Route::get('total-product-sold','ProductClaimController@totalProductSold');
        Route::get('total-product-pv','ProductClaimController@totalProductPV');
        Route::get('sum-claimed-products','ProductClaimController@sumClaimedProducts');
    });
});

Route::group(['prefix'=>'withdrawals','middleware'=>['auth:sanctum'], 'namespace'=>'App\Http\Controllers'], function() {
    Route::post('{uuid}/initiate','WithdrawalController@initiate');
    Route::get('balance-check','WithdrawalController@providerBalanceCheck');
    Route::get('{uuid}/history','WithdrawalController@history');
    Route::get('all','WithdrawalController@all');
    Route::get('{uuid}/user-history','WithdrawalController@userHistory');
    Route::get('total','WithdrawalController@total');
    Route::get('{uuid}/user-total','WithdrawalController@userTotal');
    Route::get('{id}/details','WithdrawalController@details');
});
