<?php
namespace App\Services;

use App\Repositories\AdminRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class AdminService{

    private $service;
    private $userRepository;
    private $walletAccountService;
    private $adminRepository;

    function __construct()
    {
        $this->adminRepository = new AdminRepository();
    }

    public function checkVerificationCode($email,$code)
    {
        return $this->adminRepository->checkVerificationCode($email,$code);
    }

    public function updatePassword(string $email, string $password)
    {
        $password = bcrypt($password);
        return $this->adminRepository->updatePassword($email,$password);
    }

    public function updateVerificationCode($email,$code)
    {
        return $this->adminRepository->updateVerificationCode($email,$code);
    }

    public function userExists(string $email, $model=false)
    {
        return $this->adminRepository->userExists($email,$model);
    }

    public function toggle2Fa(string $email,$data)
    {
        return $this->adminRepository->toggle2Fa($email,$data);
    }
}