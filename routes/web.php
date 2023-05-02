<?php

use Illuminate\Support\Facades\Route;
use App\Models\Setting;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $data = Setting::find(1);
    return view('welcome',['data'=>$data]);
});

Route::get('/invite-guest-mail', function () {
    $data = [
        'sender_name'=>'Josh larry',
        'sender_username'=>'josh',
        'referrer'=>'simon'
    ];
 
    return new App\Mail\InviteGuestMail($data);
});

Route::get('/password-reset-mail', function () {
    $data = '1234';
    return new App\Mail\PasswordResetEmail($data);
});

Route::get('/registration-mail', function () {
    $data = [
        'name'=>'Josh larry',
        'message'=>'A successful registration',
    ];
 
    return new App\Mail\RegistrationMail($data);
});

Route::get('/send-message-mail', function () {
    $data = [
        'subject'=>'Hey Josh',
        'body'=>'Please pay up your money',
    ];
 
    return new App\Mail\SendMessage($data);
});

Route::get('/two-factor-mail', function () {
    $data = '1234';
 
    return new App\Mail\TwoFactorVerifyMail($data);
});

Route::get('/confirmation-mail', function () {
    $data = ['verification_code'=>'1234'];
 
    return new App\Mail\UserConfirmationEmail($data);
});

Route::get('/withdrawal-mail', function () {
    $data = ['name'=>'Innocent Aluu',
    'messag'=>'
    Your withdrawal request of the amount ₦25,000 has been 
    successfully processed. The funds will be transferred to 
    your designated account within the next 1-3 business days.
    '];
 
    return new App\Mail\WithdrawalMail($data);
});

Route::get('/payment-mail', function () {
    $data = ['name'=>'Innocent Aluu','messag'=>'
    Thank you for taking the first step to register as a partner 
    with Delishcare. Your enthusiasm and quick response is admirable.
     As we move forward, I encourage you to complete the remaining 
     steps of the registration process by verifying your email and 
    proceeding with payment to finalize your registration.'];
 
    return new App\Mail\PaymentMail($data);
});




