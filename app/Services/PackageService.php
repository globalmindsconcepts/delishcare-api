<?php
namespace App\Services;

use App\Repositories\PackageRepository;
use \Exception;
class PackageService{
    private $packageRepo;
    public function __construct(){
        $this->packageRepo = new PackageRepository;
    }

    public function all()
    {
        try {
            $packages = $this->packageRepo->all();
            return ['data' => $packages, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500, 'success'=>false];
        }
    }

    public function create(array $data)
    {
        try {
           $package = $this->packageRepo->create($data);
            return ['data' => $package, 'message' => 'Package created succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['data' => $package, 'message' => $message, 'status' => 500];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $this->packageRepo->update($id, $data);
            return ['message' => 'Package updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function delete(int $id)
    {
        try {
            $this->packageRepo->table->delete($id);
            return ['message' => 'package deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}