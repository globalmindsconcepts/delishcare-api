<?php
namespace App\Services;

use App\Repositories\ProductServiceRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class ProductService extends BaseService{

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
            return $this->logger($e,"Error fetching all product services");
        }
    }

    public function create(array $data)
    {
        try {
           $data= $this->productServiceRepo->create($data);
            return ['data' => $data, 'message' => 'Provider created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,'Error creating product service');
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
            return $this->logger($e,"Error updating product service");
        }
    }

    public function delete(int $id)
    {
        try {
            $this->productServiceRepo->table->delete($id);
            return ['message' => 'Product service deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error deleting product service");
        }
    }

    public function get(Int $id)
    {
        try {
            $data = $this->productServiceRepo->getServiceById($id);
            return ['data' => $data, 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching a product service");
        }
    }
}