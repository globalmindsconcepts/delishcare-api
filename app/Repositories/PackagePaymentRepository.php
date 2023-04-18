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
        return  (new PackagePayment($data))->save(); //$this->table->insert($data);
    }

    public function get($id)
    {
       return $this->table->where('id',$id)->get();
    }

    public function getData($param,$value)
    {
        return PackagePayment::where('user_uuid','=', $value)->get();
    }

    public function totalRegistrationPV()
    {
        return $this->table->get()->sum('point_value');
    }

    public function totalRegistration($count=false)
    {
        return $this->table->get()->sum('point_value');
    }

    public function paidUsers($count=false)
    {
        if($count == false){
         return $this->table->leftJoin('users','users.uuid','=','package_payments.user_uuid')
            ->leftJoin('packages','users.package_id','=','packages.id')
            ->where('package_payments.status','=','approved')
            ->select(['users.first_name','users.last_name','packages.name','packages.vip','package_payments.created_at','package_payments.amount']) ->paginate(20);
        }
        return $this->table->get()->count();
    }

    public function sumPaidUsers()
    {
        return $this->table->where('status','=','approved')->get()->sum('amount');
    }

}