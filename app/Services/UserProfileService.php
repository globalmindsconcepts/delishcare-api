<?php
namespace App\Services;

use App\Repositories\UserProfileRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class UserProfileService extends BaseService{

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
            return $this->logger($e,"Error creating profile");
        }
    }

    public function get(string $user_uuid)
    {
        try {
            $profile = $this->profileRepo->get($user_uuid);
            return ['data'=>$profile,'status'=>200,'success'=>true];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching profile");
        }
    }

    public function update(string $user_uuid, array $data)
    {
        try {
            unset($data['image']);
            $this->profileRepo->update($user_uuid,$data);
            return ['message' => 'Profile updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error updating profile");
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
            return $this->logger($e,"Error updating bank details");
        }
    }

    public function toggle2FA(string $uuid, array $data)
    {
        try {
            //$enable = $data['enable_2fa'] == 'true' || $data['enable_2fa'] == true ? true : false;
            $this->profileRepo->toggle2FA($uuid,$data);
            
           return ['success'=>true,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error toggling 2FA");
        }
    }

    public function bankEditable(string $uuid, array $data)
    {
        try {
            $enable = $data['bank_editable'] == true ? true : false;
            $this->profileRepo->toggleBankEditable($uuid,$enable);
            
           return ['success'=>true,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error toggling bank editable");
        }
    }
}