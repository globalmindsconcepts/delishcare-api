<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\ProductService;

class ProductServiceRepository {

    public $table, $model;
    public function __construct(){
        $this->table = DB::table('product_services');
        $this->model = new ProductService;
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO product_services (service,default_provider_id,created_at) VALUES (?,?,?)";
        $res = DB::insert($sql,[$data['service'],$data['default_provider_id'],now()]);
        return $res;
    }

    /**
     * update a charge
     */
    public function update($id,array $data)
    {
        $sql = "UPDATE product_services SET service = ?, default_provider_id=?, updated_at=? WHERE id = '$id'";
        $res = DB::update($sql,[$data['service'],$data['default_provider_id'],now()]);
        return $res;
    }

    /**
     * check if recorod already exist during update
     */
    public function checkUpdate($id,string $service)
    {
        //$service = $data['service'];
        $sql = "SELECT * FROM product_services WHERE service='$service' AND id <> '$id' LIMIT 1";
        $res = DB::selectOne($sql);
        $data = $res;
        if(empty($data)){
            return null;
        }
        return (array)$data[0];
    }

    public function getService(string $service)
    {
        $sql = "SELECT * FROM product_services INNER JOIN service_providers 
        ON product_services.default_provider_id = service_providers.id WHERE service='$service' LIMIT 1";
        $res = DB::select($sql);
        $data = $res;
        if(empty($data)){
            return null;
        }
        return (array)$data[0];
    }

    public function getServiceDetails(string $service)
    {
        $sql = "SELECT * FROM product_services 
        WHERE service='$service' LIMIT 1";
        $res = DB::select($sql);
        $data = $res;
        if(empty($data)){
            return null;
        }
        return (array)$data[0];
    }

    public function getServiceById($id)
    {
        $sql = "SELECT * FROM product_services WHERE id='$id' LIMIT 1";
        $res = DB::selectOne($sql);
        $data = (array)$res[0];
        if(empty($data)){
            return null;
        }
        return $data;
    }

    public function getServices()
    {
        $sql = "SELECT product_services.id,name,product_services.created_at,service,default_provider FROM product_services LEFT JOIN service_providers 
        ON product_services.default_provider_id = service_providers.id";
        $res = DB::select($sql);
        return (array)$res;
    }
}