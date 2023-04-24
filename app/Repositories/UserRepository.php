<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository{

    private $model;
    public $table;
    private $user; //= User::class;

    public function __construct(){
        $this->model = new User;
        $this->table = DB::table('users');
        $this->user = User::class;
        
    }

    public function create(array $data)
    {
        unset($data['referrer']);
        unset($data['placer']);
        //info('user',[$data]);
       return (new User($data))->save(); //$this->table->insert($data);
    }

    public function update(string $uuid, array $data)
    {
        return $this->table->where('uuid', $uuid)->update($data);
    }

    public function activeUsers(bool $count)
    {
        if ($count) {
            $sql = "SELECT COUNT(id) as total FROM users WHERE EXISTS 
            (SELECT id FROM package_payments WHERE users.uuid = package_payments.user_uuid)";
            $data = DB::select($sql);
            return $data[0]->total;
        }
        $data = DB::table('users')->join('package_payments', 'users.uuid', '=', 'package_payments.user_uuid')->paginate(50);
        return $data;
    }

    public function getUser($param)
    {
        $sql = "SELECT * FROM users WHERE users.uuid = ? OR users.username=? LIMIT 1";
        $res = DB::select($sql,[$param,$param]);
        if (!empty($res)) {
            return (array) $res[0];
        }
        return null;
    }

    public function all(bool $count = false)
    {
        if ($count) {
            $data = DB::table('users')->count();
            return $data;
        }
        $data = DB::table('users')->leftJoin('user_profiles', 'users.uuid', '=', 'user_profiles.user_uuid')
        ->leftJoin('packages', 'users.package_id', '=', 'packages.id')
        ->leftJoin('package_payments','package_payments.user_uuid','=','users.uuid') //delete later
        ->where('users.id','<>',1)
        ->orderByDesc('users.created_at')->paginate(50);
        return $data;
    }

    public function search($data)
    {
        $data = DB::table('users')->leftJoin('user_profiles', 'users.uuid', '=', 'user_profiles.user_uuid')
        ->leftJoin('packages', 'users.package_id', '=', 'packages.id')
        ->where('users.id','<>',1)
        ->where($data)
        ->orderByDesc('users.created_at')->paginate(50);
        return $data;
    }

    public function details($user_uuid)
    {
        $sql = "SELECT * FROM users
        LEFT JOIN user_profiles 
        ON users.uuid = user_profiles.user_uuid
        WHERE users.uuid = '$user_uuid' LIMIT 1";
        $res = DB::select($sql);
        return (array) $res[0];
    }

    public function userExists(string $email,$model=false)
    {
        if($model){
            return User::where('email',$email)->orWhere('username','=',$email)->first();//->createToken();
        }
        $data = DB::table('users')->where('email', '=', $email)->orWhere('username','=',$email)->first();
        return $data;
    }

    public function updateVerificationCode(string $email, string $code)
    {
        $res = DB::table('users')->where('email', '=', $email)->update(['verification_code' => $code]);
        return $res;
    }

    public function checkVerificationCode(string $email, string $code)
    {
        $data = DB::table('users')->where('email', '=', $email)->orWhere('username', '=', $email)->get();//->first();
        if($data->first()){
           return $data->where('verification_code', '=', $code)->first();
        }
        
    }

    public function updatePassword(string $email, string $password)
    {
        //info($email);
        $res = DB::table('users')->where('email', '=', $email)->update(['verification_code' => null, 'password' => $password]);
        return $res;
    }

    public function verifyEmail(string $email)
    {
        $res = DB::table('users')->where('email', '=', $email)->orWhere('username','=',$email)->update(['email_verified_at' => now()]);
        return $res;
    }

    public static function uuidExists(string $uuid)
    {
        $sql = "SELECT COUNT(id) as total FROM users
        WHERE uuid = '$uuid' LIMIT 1";
        $res = DB::select($sql);
        if ($res[0]->total == 0) {
            return false;
        }
        return true;
    }

    public function delete(string $uuid)
    {
        $sql = "DELETE FROM users
        WHERE user_uuid = '$uuid'";
        $res = DB::delete($sql);
        return $res;
    }

    public function totalRegistrations()
    {
        return $this->table->get()->count();
    }

}