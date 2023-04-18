<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\UserService;
use App\Services\AdminService;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\EmailConfirmationRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\Authenticate2FA;
use App\Mail\PasswordResetEmail;
use App\Mail\TwoFactorVerifyMail;
use \Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Requests\ChangeUserPassword;

class AuthController extends Controller
{
    private $userService;
    private $adminService;

    public function __construct()
    {
        $this->userService = new UserService;
        $this->adminService = new AdminService;
    }
    
    public function login(Request $request)
    {
        try{
            //dd($request->all());
            $credentials = $request->only('username', 'password');
            if (!Auth::attempt($credentials)) {
                return response()->json(["message"=>"Invalid login credentials"],400);
            }

            $user = $request->user();
            $enable_2fa = false;
            if($profile=$user->profile()->first()){
                $enable_2fa = $profile->enable_2fa;
            }

            $token = $user->createToken('authToken');//->plainTextToken;
            if ($request->remember_me){}

            $payment = $user->packagePayment()->first();
            
            $resource = [
                'access_token' => $token->plainTextToken,
                'email_verified_at'=> $user->email_verified_at,
                'enable_2fa'=>$enable_2fa,
                //'details'=>$user,
                'payment'=>$payment
                //'token_type' => 'Bearer',
                //'user'=>$user->load('level')
                // 'expires_at' => Carbon::parse(
                //     $tokenResult->token->expires_at
                // )->toDateTimeString()
            ];
            return response()->json(["message"=>"Logged in successfully",'data'=>$resource],200);
        }catch(Exception $e){
            Log::error("Error while loggingin",[$e]);
            return response()->json(["message"=>"An internal error occured"],500);
        }
    }

    public function adminLogin(Request $request)
    {
        try{
            info($request->all());
            $credentials = $request->only('email', 'password');
            if (!Auth::guard('admin')->attempt($credentials)) {
                return response()->json(["message"=>'Invalid login credentials'],400);
            }
            $user = $request->user('admin');
            $enable_2fa = $user->enable_2fa;

            $token = $user->createToken('authToken');
            
            $resource = [
                'access_token' => $token->plainTextToken,
                //'details'=>$user,
                'email_verified_at'=> $user->email_verified_at,
                'is_admin'=>true,
                'enable_2fa'=>$enable_2fa
            ];
            return response()->json(["message"=>"Logged in successfully",'data'=>$resource],200);
        }catch(Exception $e){
            Log::error("Error while logging in",[$e]);
            return response()->json(["message"=>"An internal error occured"],500);
        }
    }

    public function register(RegistrationRequest $request)
    {
        $res = $this->userService->create($request->only('first_name','package_id',
        'last_name','email','phone','password','username','referrer','placer'));
        return response()->json($res,$res['status']);
    }

    public function emailConfirmation(EmailConfirmationRequest $request)
    {
        try {
            $code = $request->code;
            $email = $request->email;
            $userType = $request->user_type;
            $service = $this->userService;
            if($service->checkVerificationCode($email,$code) && $service->verifyEmail($email)){
                return response()->json(["message"=>"Email verified successfully"]);
            }
            return response()->json(["message"=>"Invalid verification code"],400);
        } catch (Exception $e) {
            Log::error("Error confirming email",[$e]);
            return response()->json(["message"=>"An error occured"],500);
        }
    }

    public function resendEmailConfirmationCode(Request $request)
    {
        try {
            $email = $request->email;
            $service = $this->userService;
            if($user = $service->userExists($email)){
                $service->resendEmailConfirmationCode($user->email);
                return response()->json(["message"=>"Verification code resent successfully"]);
            }
            return response()->json(["message"=>"Email does not exists"],400);
        } catch (Exception $e) {
            Log::error("Error confirming email",[$e]);
            return response()->json(["message"=>"An error occured"],500);
        }
    }

    public function resetPassword(PasswordResetRequest $request)
    { 
        try {
            $code = $request->code;
            $email = $request->email;
            $password = $request->password;
            $userType = $request->user_type;
            $service = $userType == 'user' ? $this->userService : $this->adminService;
            if($service->checkVerificationCode($email,$code)){
                $service->updatePassword($email,$password);
                return response()->json(["message"=>"Password reset successfully"]);
            }
            return response()->json(["message"=>"Invalid verification code"],400);
        } catch (Exception $e) {
            Log::error("Error reseting password", [$e]);
            return response()->json(["message"=>"An error occured, please try again"],500);
        }
    }

    public function authenticate2FA(Authenticate2FA $request)
    { 
        try {
            $code = $request->code;
            $email = $request->email;
            $userType = $request->user_type;
            $service = $userType == 'user' ? $this->userService : $this->adminService;
            
            if($service->checkVerificationCode($email,$code)){
                $data = $service->userExists($email,true); 
                $resource = [
                    'access_token' => $data->createToken('authToken')->plainTextToken,
                    'is_admin'=> $userType == 'admin' ? true : false
                ];
                return response()->json(['data'=>$resource, "message"=>"2FA verified successfully"]);
            }
            return response()->json(["message"=>"Invalid verification code"],400);
        } catch (Exception $e) {
            Log::error("Error verifying 2FA", [$e]);
            return response()->json(["message"=>"An error occured, please try again"],500);
        }
    }

    public function sendPasswordResetEmail(Request $request)
    {
        try {
            $verifyCode = Str::random(5);
            $data['verification_code'] = $verifyCode;
            $email = $request->email;
            $userType = $request->user_type;
            $service = $userType == 'user' ? $this->userService : $this->adminService;
            if($service->userExists($email)){
                $service->updateVerificationCode($email,$verifyCode);
                Mail::to($email)->queue(new PasswordResetEmail($verifyCode));
                return response()->json(["message"=>"Please check your email for the password reset code"]);
            }
            return response()->json(["message"=>"Error, Email does not exist"],400);
        } catch (Exception $e) {
            Log::error("Error sending password reset email",[$e]);
            return response()->json(["message"=>"An error occured, please try again"],500);
        }
    }

    public function sendPasswordChangeEmail(Request $request)
    {
        try {
            $verifyCode = Str::random(5);
            $data['verification_code'] = $verifyCode;
            $email = $request->email;
            $oldPassword = $request->old_password;
            $userType = $request->user_type;
            $service = $userType == 'user' ? $this->userService : $this->adminService;
            $user = $service->userExists($email);
            if($user && Hash::check($oldPassword,$user->password)){
                $service->updateVerificationCode($email,$verifyCode);
                Mail::to($email)->queue(new PasswordResetEmail($verifyCode));
                return response()->json(["message"=>"Please check your email for the password reset code"]);
            }
            return response()->json(["message"=>"Email or old password does not exist"],400);
        } catch (Exception $e) {
            Log::error("Error sending password reset email",[$e]);
            return response()->json(["message"=>"An error occured, please try again"],500);
        }
    }

    public function send2faEmail(Request $request)
    {
        try {
            $verifyCode = Str::random(5);
            $data['verification_code'] = $verifyCode;
            $email = $request->email;
            $userType = $request->user_type;
            $service = $userType == 'user' ? $this->userService : $this->adminService;
            if($user = $service->userExists($email)){
                $service->updateVerificationCode($user->email,$verifyCode);
                Mail::to($user->email)->queue(new TwoFactorVerifyMail($verifyCode));
                return response()->json(["message"=>"Please check your email for the 2FA authentication code"]);
            }
            return response()->json(["message"=>"Error, Email does not exist"],400);
        } catch (Exception $e) {
            Log::error("Error sending 2FA email",[$e]);
            return response()->json(["message"=>"An error occured, please try again"],500);
        }
    }

    public function authUser(Request $request)
    {
        $user = $request->user();
        return response()->json($user);
    }

    public function authAdmin(Request $request)
    {
        $user = $request->user();
        return response()->json($user);
    }

    //admin change user password
    public function changeUserPassword(ChangeUserPassword $request, string $uuid)
    {
        $user = (new \App\Repositories\UserRepository)->getUser($uuid);
        $email = $user['email'];
        try {
            $this->userService->updatePassword($email,$request->password);
            //send email to user
            return response()->json(["message"=>"Password reset successfully"]);
        } catch (Exception $e) {
            Log::error("Error changing user password",[$e]);
            return response()->json(["message"=>"An error occured, please try again"],500);
        }
    }

    public function toggleAdmin2fa(Request $request)
    {
        try {
            $admin = $request->user();
            $email = $admin->email;
            $this->adminService->toggle2fa($email,$request->only('enable_2fa'));
            return response()->json(["message"=>"2FA toggled successfully"]);
        } catch (Exception $e) {
            Log::error("Error enabling admin 2fa",[$e]);
            return response()->json(["message"=>"An error occured, please try again"],500);
        }
    }
}
