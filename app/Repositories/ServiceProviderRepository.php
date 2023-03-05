<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\ServiceProvider;

class ServiceProviderRepository {

    private $model;
    public $table;
    public function __construct(){
        $this->model = new ServiceProvider;
        $this->table = DB::table('service_providers');
    }

    public function all()
    {
        return $this->table->paginate(20);
    }

    public function get(Int $id)
    {
        return $this->table->find($id);
    }

    public function create(array $data)
    {
        return $this->table->insert($data);
    }

    /**
     * update a charge
     */
    public function update($id, array $data)
    {
        //return $this->table->where('id', '=', $id)->update($data);
        $sql = "UPDATE service_providers SET name = ?, updated_at=? WHERE id = '$id'";
        $res = DB::update($sql,[$data['name'],now()]);
        return $res;
    }

    /**
     * check if recorod already exist during update
     */
    public function checkUpdate($id,string $name)
    {
        //$service = $data['service'];
        $sql = "SELECT * FROM service_providers WHERE name='$name' AND id <> '$id' LIMIT 1";
        $res = DB::selectOne($sql);
        $data = $res;
        if(empty($data)){
            return null;
        }
        return (array)$data;
    }

    public function getProvider(string $name)
    {
        $sql = "SELECT * FROM service_providers WHERE name='$name' LIMIT 1";
        $res = DB::selectOne($sql);
        $data = (array)$res;
        if(empty($data)){
            return null;
        }
        return $data;
    }

    public function getProviderById($id)
    {
        $sql = "SELECT * FROM service_providers WHERE id='$id' LIMIT 1";
        $res = DB::selectOne($sql);
        $data = $res;
        if(empty($data)){
            return null;
        }
        return (array)$data;
    }

    public function getProviders()
    {
        $sql = "SELECT * FROM service_providers";
        $res = DB::select($sql);
        return (array)$res;
    }
}