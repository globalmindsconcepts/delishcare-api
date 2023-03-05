<?php
namespace App\Services;

use App\Repositories\ServiceProviderRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class ServiceProviderService{

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
            Log::error('Error fetching providers', [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function get(Int $id)
    {
        try {
            $data = $this->serviceProviderRepo->get($id);
            return ['data' => $data, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            Log::error('Error getting providers', [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function create(array $data)
    {
        try {
           $data = $this->serviceProviderRepo->create($data);
            return ['data' => $data, 'message' => 'Provider created succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error('error creating provider', [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $this->serviceProviderRepo->update($id, $data);
            return ['message' => 'Provider updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error('Error updating provider', [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function delete(int $id)
    {
        try {
            $this->serviceProviderRepo->table->delete($id);
            return ['message' => 'RProvider deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error('Error deleting provider', [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}