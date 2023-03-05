<?php
namespace App\Services;

use App\Repositories\PackagePaymentRepository;
use \Exception;
class PackagePaymentService{
    private $packagePaymentRepo;
    public function __construct(){
        $this->packagePaymentRepo = new PackagePaymentRepository;
    }

    public function all()
    {
        try {
            $incentives = $this->packagePaymentRepo->all();
            return ['data' => $incentives, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500, 'success'=>false];
        }
    }

    public function create(array $data)
    {
        try {
           $data = $this->packagePaymentRepo->create($data);
            return ['data' => $data, 'message' => 'Package created succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function delete(int $id)
    {
        try {
            $this->packagePaymentRepo->table->delete($id);
            return ['message' => 'package deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}