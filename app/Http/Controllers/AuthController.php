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
use App\Mail\PasswordResetEmail;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                return response()->json(["message"=>"Invalid login credentials"],400);
                //return $this->errorResponse('Invalid login credentials');
            }
            $user = $request->user();
            $token = $user->createToken('authToken');//->plainTextToken;
            if ($request->remember_me){
                //$token->expires_at = Carbon::now()->addWeeks(1);
                //$token->save();
            }
            $resource = [
                'access_token' => $token->plainTextToken,
                'email_verified_at'=> $user->email_verified_at,
                //'token_type' => 'Bearer',
                //'user'=>$user->load('level')
                // 'expires_at' => Carbon::parse(
                //     $tokenResult->token->expires_at
                // )->toDateTimeString()
            ];
            return response()->json(["message"=>"Logged in successfully",'data'=>$resource],200);
        }catch(\Exception $e){
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
            $token = $user->createToken('authToken');
            
            $resource = [
                'access_token' => $token->plainTextToken,
                'email_verified_at'=> $user->email_verified_at,
            ];
            return response()->json(["message"=>"Logged in successfully",'data'=>$resource],200);
        }catch(Exception $e){
            Log::error("Error while logging in",[$e]);
            return response()->json(["message"=>"An internal error occured"],500);
        }
    }

    public function register(RegistrationRequest $request)
    {
        $res = $this->userService->create($request->only('first_name',
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
            if($service->userExists($email)){
                $service->resendEmailConfirmationCode($email);
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

    public function authUser(Request $request)
    {
        $user = $request->user();
        //info()
        return response()->json($user);
    }

    public function authAdmin(Request $request)
    {
        $user = $request->user('admin');
        //info()
        return response()->json($user);
    }
}
