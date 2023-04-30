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
 
    return new App\Mail\InviteGuestMail($data);
});

Route::get('/two-factor-mail', function () {
    $data = '1234';
 
    return new App\Mail\TwoFactorVerifyMail($data);
});

Route::get('/confirmation-mail', function () {
    $data = ['verification_code'=>'1234'];
 
    return new App\Mail\UserConfirmationEmail($data);
});


