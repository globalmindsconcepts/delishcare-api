<?php
namespace App\Repositories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PackageRepository{
    private $model;
    public $table;
    private $package;
    public function __construct(){
        $this->model = new Package;
        $this->table = DB::table('packages');
    }

    public function all()
    {
        return $this->table->paginate(20);
    }

    public function create(array $data)
    {
        return (new Package($data))->save();
    }

    public function update(int $id, array $data)
    {
        info('id',[$id]);
        //return $this->table->where('id',$id)->update($data);

        $sql = "UPDATE packages SET registration_value=?, name=?, point_value=? WHERE id=?";
        $res = DB::select($sql,[$data['registration_value'],$data['name'],$data['point_value'],$id]);
        return $res;
        //info('upd', [$upd]);
    }

    public function get($id)
    {
       return $this->table->where('id','=',$id)->get()->first();
    }

    public function delete(int $id)
    {
        return $this->table->delete($id);
    }

    public function checkPackage(Int $id, array $data)
    {
        return $this->table->select('id')->where($data)->where('id', '<>', $id)->get()->first();
    }

}