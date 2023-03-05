<?php
namespace App\Services;

use App\Repositories\UserProfileRepository;
use \Exception;
class UserProfileService{

    private $profileRepo;
    public function __construct(){
        $this->profileRepo = new UserProfileRepository;
    }

    public function create(array $data)
    {
        try {
           $profile = $this->profileRepo->create($data);
            return ['data' => $profile, 'message' => 'Profile created succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function update(string $user_uuid, array $data)
    {
        try {
            $this->profileRepo->update($user_uuid,$data);
            return ['message' => 'Profile updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}