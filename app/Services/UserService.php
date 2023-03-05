<?php
namespace App\Services;

//use App\Interfaces\UserInterface;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\UserConfirmationEmail;
use Illuminate\Support\Facades\Mail;
use App\Repositories\ReferralRepository;
use Illuminate\Support\Facades\DB;

class UserService{

    private $service;
    private $userRepository;
    private $referralRepository;

    function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->referralRepository = new ReferralRepository;
    }

    public function all(bool $count=false)
    {
        try {
            $data = $this->userRepository->all($count);
        } catch (Exception $e) {
            Log::error("Error fetching users",[$e]);
            return ["success"=>false,"message"=>$e->getMessage(),"status"=>500];
        }

        return ["success"=>true, "data"=>$data,
        "message"=>$count ? 'Total users': 'Users'."fetched sucessfully","status"=>200];
    }
    
    public function activeUsers(bool $count=false)
    {
        try {
            $data = $this->userRepository->activeUsers($count);
        } catch (Exception $e) {
            Log::error("Error fetching active users",[$e]);
            return ["success"=>false,"message"=>$e->getMessage(),"status"=>500];
        }

        return ["success"=>true, "data"=>$data,
        "message"=>$count ? 'Total active users': 'Users'."fetched sucessfully","status"=>200];
    }

    public function create(array $data)
    {
        try {
            
            $data['uuid'] = $this->generateUUID();
            $data['password'] = bcrypt($data['password']);
            $verifyCode = Str::random(5);
            $data['verification_code'] = $verifyCode;
            $referer = $this->userRepository->getUser($data['referrer']);
            info('ref',[$referer]);
            $placement = array_key_exists('placer',$data) ? $this->userRepository->getUser($data['placer'])['uuid'] : null;

            DB::transaction(function () use ($data,$referer,$placement) {
                $this->userRepository->create($data);
                $refData = ['referrer_uuid'=>$referer['uuid'],'referred_uuid'=>$data['uuid'],'placer_uuid'=>$placement];
                $this->referralRepository->create($refData);
            });
            
            Mail::to($data['email'])->queue((new UserConfirmationEmail($data)));

        } catch (Exception $e) {
            Log::error("Error creating user",[$e]);
            return ["success"=>false,"message"=>$e->getMessage(),"status"=>500];
        }
        return ["success"=>true,
        "message"=>"user created sucessfully","status"=>200];
    }

    public function details(string $uuid)
    {
        try {
            $data = $this->userRepository->details($uuid);
        } catch (Exception $e) {
            Log::error("Error fetching user",[$e]);
            return ["success"=>false,"message"=>$e->getMessage(), "status"=>500];
        }
        return ["success"=>true, "data"=>$data,
        "message"=>"user fetched sucessfully","status"=>200];
    }

    /**
     * check user's email exists for password reset
     */
    public function userExists(string $email)
    {
       return $this->userRepository->userExists($email);
    }

    public function updateVerificationCode($email,$code)
    {
        return $this->userRepository->updateVerificationCode($email,$code);
    }

    public function checkVerificationCode($email,$code)
    {
        return $this->userRepository->checkVerificationCode($email,$code);
    }

    public function updatePassword(string $email, string $password)
    {
        $password = bcrypt($password);
        return $this->userRepository->updatePassword($email,$password);
    }

    public function verifyEmail(string $email)
    {
        return $this->userRepository->verifyEmail($email);
    }

    public function resendEmailConfirmationCode($email)
    {
        $verifyCode = Str::random(5);
        $data['verification_code'] = $verifyCode;
        $this->userRepository->updateVerificationCode($email,$verifyCode);
        Mail::to($email)->queue((new UserConfirmationEmail($data)));
    }

    /**
     * admin get user details
     */
    public function userDetails(string $user_uuid)
    {
        try {
            $data = $this->userRepository->getUserDetails($user_uuid);
        } catch (Exception $e) {
            Log::error("Error fetching user",[$e]);
            return ["success"=>false,"message"=>$e->getMessage(), "status"=>500];
        }
        return ["success"=>true, "data"=>$data,
        "message"=>"user fetched sucessfully","status"=>200];
    }

    public function delete(string $user_uuid)
    {
        try {
            $this->userRepository->delete($user_uuid);
        } catch (Exception $e) {
            return ["success"=>false,"message"=>$e->getMessage(),"status"=>500];
        }
        return ["success"=>true,
        "message"=>"user deleted sucessfully","status"=>200];
    }

    public function update(string $user_uuid, array $data)
    {
        try {
           $this->userRepository->update($user_uuid,$data);
        } catch (Exception $e) {
            Log::error("Error updating user",[$e]);
            return ["success"=>false,"message"=>$e->getMessage(),"status"=>500];
        }
        return ["success"=>true,
        "message"=>"user updated sucessfully","status"=>200];
    }

    protected function generateUUID()
    {
        $uuid = Str::random(16);
        if($this->userRepository->uuidExists($uuid)){
            $this->generateUUID();
        }
        return $uuid;
    }


}
