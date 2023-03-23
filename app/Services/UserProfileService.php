<?php
namespace App\Services;

use App\Repositories\UserProfileRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class UserProfileService{

    private $profileRepo;
    public function __construct(){
        $this->profileRepo = new UserProfileRepository;
    }

    public function create(string $user_uuid, array $data)
    {
        try {
            unset($data['image']);
            $data['user_uuid'] = $user_uuid;
            $profile = $this->profileRepo->create($data);
            return ['success' => true, 'message' => 'Profile created succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error("error creating profile",[$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function get(string $user_uuid)
    {
        try {
            $profile = $this->profileRepo->get($user_uuid);
            return ['data'=>$profile,'status'=>200,'success'=>true];
        } catch (Exception $e) {
            Log::error("error fetching profile",[$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function update(string $user_uuid, array $data)
    {
        try {
            unset($data['image']);
            $this->profileRepo->update($user_uuid,$data);
            return ['message' => 'Profile updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error("error updating profile",[$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function updateBankDetails(string $user_uuid, array $data)
    {
        try {
            //$data['bank_account_name']=$data['account_name'];
            //$data['bank_account_number']=$data['account_number'];
            $this->profileRepo->update($user_uuid,$data);
            return ['success'=>true, 'status' => 200];
        } catch (Exception $e) {
            Log::error("error updating bank details",[$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}