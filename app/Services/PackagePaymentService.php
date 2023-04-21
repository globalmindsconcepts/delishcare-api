<?php
namespace App\Services;

use App\Repositories\PackagePaymentRepository;
use \Exception;
class PackagePaymentService extends BaseService{
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
            return $this->logger($e,"Error fetching all package payments");
        }
    }

    public function create(array $data)
    {
        try {
           $data = $this->packagePaymentRepo->create($data);
            return ['data' => $data, 'message' => 'Package created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error creating package payment");
        }
    }

    public function delete(int $id)
    {
        try {
            $this->packagePaymentRepo->table->delete($id);
            return ['message' => 'package deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error deleting package payment");
        }
    }
}