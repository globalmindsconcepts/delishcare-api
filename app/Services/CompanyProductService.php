<?php
namespace App\Services;

use App\Repositories\ProductRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class CompanyProductService extends BaseService{
    private $productRepo;
    public function __construct(){
        $this->productRepo = new ProductRepository;
    }

    public function all()
    {
        try {
            $products = $this->productRepo->all();
            return ['data' => $products, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            return $this->logger($e,'error fetching products');
        }
    }

    public function get(Int $id)
    {
        try {
            $product = $this->productRepo->get($id);
            return ['data' => $product, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            return $this->logger($e,'error getting product');
        }
    }

    public function create(array $data)
    {
        try {
           $package = $this->productRepo->create($data);
            return ['data' => $package, 'message' => 'Product created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,'error creating product');
        }
    }

    public function update(int $id, array $data)
    {
        try {
            if($this->productRepo->checkProduct($id,['name'=>$data['name']])){
                return ['message' => 'Package name already exists', 'status' => 400];
            }
            $this->productRepo->update($id, $data);
            return ['message' => 'Product updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,'error updating product');
        }
    }

    public function delete(int $id)
    {
        try {
            $this->productRepo->table->delete($id);
            return ['message' => 'package deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,'error deleting product');
        }
    }

    //public function 
}