<?php
namespace App\Services;

use App\Repositories\ServiceProviderRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class ServiceProviderService extends BaseService{

    private $serviceProviderRepo;
    public function __construct(){
        $this->serviceProviderRepo = new ServiceProviderRepository;
    }

    public function all()
    {
        try {
            $data = $this->serviceProviderRepo->all();
            return ['data' => $data, 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,'Error fetching providers');
        }
    }

    public function get(Int $id)
    {
        try {
            $data = $this->serviceProviderRepo->get($id);
            return ['data' => $data, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            return $this->logger($e,'Error getting providers');
        }
    }

    public function create(array $data)
    {
        try {
           $data = $this->serviceProviderRepo->create($data);
            return ['data' => $data, 'message' => 'Provider created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,'error creating provider');
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $this->serviceProviderRepo->update($id, $data);
            return ['message' => 'Provider updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,'Error updating provider');
        }
    }

    public function delete(int $id)
    {
        try {
            $this->serviceProviderRepo->table->delete($id);
            return ['message' => 'RProvider deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,'Error deleting provider');
        }
    }
}