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
            ->leftJoin('service_provider', 'service_providers.id', '=', 'product_services.default_provider_id')->paginate(20);
            return ['data' => $data, 'status' => 200];
        } catch (Exception $e) {
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
            $this->productServiceRepo->update($id, $data);
            return ['message' => 'Product service updated succesfully', 'status' => 200];
        } catch (Exception $e) {
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
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}