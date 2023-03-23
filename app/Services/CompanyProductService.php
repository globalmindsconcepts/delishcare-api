<?php
namespace App\Services;

use App\Repositories\ProductRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class CompanyProductService{
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
            Log::error('error fetching products',[$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500, 'success'=>false];
        }
    }

    public function get(Int $id)
    {
        try {
            $product = $this->productRepo->get($id);
            return ['data' => $product, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            Log::error('error getting product',[$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500, 'success'=>false];
        }
    }

    public function create(array $data)
    {
        try {
           $package = $this->productRepo->create($data);
            return ['data' => $package, 'message' => 'Package created succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error('error creating product',[$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['data' => $package, 'message' => $message, 'status' => 500];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            if($this->productRepo->checkProduct($id,['name'=>$data['name']])){
                return ['message' => 'Package name already exists', 'status' => 400];
            }
            $this->productRepo->update($id, $data);
            return ['message' => 'Package updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error('error updating product',[$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function delete(int $id)
    {
        try {
            $this->productRepo->table->delete($id);
            return ['message' => 'package deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error('error deleting product',[$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}