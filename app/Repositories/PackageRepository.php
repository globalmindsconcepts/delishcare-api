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
        return $this->model->save($data);
    }

    public function update(int $id, array $data)
    {
        return $this->table->where('id', '=', $id)->update($data);
    }

    public function get($id)
    {
       return $this->table->where('id',$id)->first();
    }

    public function delete(int $id)
    {
        return $this->table->delete($id);
    }

}