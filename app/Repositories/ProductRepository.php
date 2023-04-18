<?php
namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductRepository{
    private $model;
    public $table;

    public function __construct(){
        $this->model = new Product;
        $this->table = DB::table('products');
    }

    public function all()
    {
        return Product::all(); //$this->table->get();
    }

    public function create(array $data)
    {
        return (new Product($data))->save(); //$this->table->insert($data);
    }

    public function get($id)
    {
        return Product::find($id);
       //return $this->table->where('id',$id)->get()->first();
    }

    public function getData($param,$value)
    {
        return Product::where('user_uuid','=', $value)->get();
    }

    public function update(int $id, array $data)
    {
        return Product::where('id', '=', $id)->update($data);
    }

    public function checkProduct(Int $id, array $data)
    {
        return $this->table->select('id')->where($data)->where('id', '<>', $id)->get()->first();
    }

}