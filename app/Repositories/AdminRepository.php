<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Admin;

class AdminRepository {

    public function updateVerificationCode(string $email, string $code)
    {
        $res = DB::table('admins')->where('email','=',$email)->update(['verification_code'=>$code]);
        return $res;
    }

    public function updatePassword(string $email, string $password)
    {
        $res = DB::table('admins')->where('email','=',$email)->update(['verification_code'=>null,'password'=>$password]);
        return $res;
    }

    public function checkVerificationCode(string $email,string $code)
    {
        $data = DB::table('admins')->where('email','=',$email)->where('verification_code','=',$code)->first();
        return $data;
    }

    public function userExists(string $email, $model=false)
    {
        if($model){
            return Admin::where('email',$email)->first();//->createToken();
        }
        $data = DB::table('admins')->where('email','=',$email)->first();
        return $data;
    }

    public function toggle2Fa(string $email,array $data)
    {
        $data = DB::table('admins')->where('email','=',$email)->update($data);
        return $data;
    }
}