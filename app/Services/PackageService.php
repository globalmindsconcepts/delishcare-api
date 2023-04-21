<?php
namespace App\Services;

use App\Repositories\PackageRepository;
use \Exception;
class PackageService extends BaseService{
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
            return $this->logger($e,"Error fetching all packages");
        }
    }

    public function get(Int $id)
    {
        try {
            $package = $this->packageRepo->get($id);
            return ['data' => $package, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            return $this->logger($e,"error getting package");
        }
    }

    public function create(array $data)
    {
        try {
           $package = $this->packageRepo->create($data);
            return ['data' => $package, 'message' => 'Package created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error creating package");
        }
    }

    public function update(int $id, array $data)
    {
        try {
            if($this->packageRepo->checkPackage($id,['name'=>$data['name']])){
                return ['message' => 'Package name already exists', 'status' => 400];
            }
            $this->packageRepo->update($id, $data);
            return ['message' => 'Package updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error updating package");
        }
    }

    public function delete(int $id)
    {
        try {
            $this->packageRepo->table->delete($id);
            return ['message' => 'package deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error deleting package");
        }
    }
}