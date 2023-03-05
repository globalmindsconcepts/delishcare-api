<?php
namespace App\Repositories;

use App\Models\PackagePayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PackagePaymentRepository{
    private $model;
    public $table;

    public function __construct(){
        $this->model = new PackagePayment;
        $this->table = DB::table('package_payments');
    }

    public function all()
    {
        return PackagePayment::all(); //$this->table->get();
    }

    public function create(array $data)
    {
        return $this->table->insert($data);
    }

    public function get($id)
    {
       return $this->table->where('id',$id)->get();
    }

    public function getData($param,$value)
    {
        return PackagePayment::where('user_uuid','=', $value)->get();
    }

}