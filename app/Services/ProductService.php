<?php
namespace App\Services;

use App\Repositories\ProductServiceRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class ProductService{

    private $productServiceRepo;
    public function __construct(){
        $this->productServiceRepo = new ProductServiceRepository;
    }

    public function all()
    {
        try {
            $data = $this->productServiceRepo->table
            ->leftJoin('service_providers', 'service_providers.id', '=', 'product_services.default_provider_id')->paginate(20);
            return ['data' => $data, 'status' => 200];
        } catch (Exception $e) {
            Log::error("all services", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function create(array $data)
    {
        try {
           $data= $this->productServiceRepo->create($data);
            return ['data' => $data, 'message' => 'Provider created succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error('Error creating product service',[$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            // if($this->productServiceRepo->checkUpdate($id,['service'=>$data['service']])){

            // }
            $this->productServiceRepo->update($id, $data);
            return ['message' => 'Product service updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error("update service", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function delete(int $id)
    {
        try {
            $this->productServiceRepo->table->delete($id);
            return ['message' => 'Product service deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error("delete service", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function get(Int $id)
    {
        try {
            $data = $this->productServiceRepo->getServiceById($id);
            return ['data' => $data, 'status' => 200];
        } catch (Exception $e) {
            Log::error("get service", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}