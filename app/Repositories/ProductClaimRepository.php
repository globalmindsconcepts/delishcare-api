<?php
namespace App\Repositories;

use App\Models\ProductClaim;
use Illuminate\Support\Facades\DB;
use \Exception;

class ProductClaimRepository{

    private $model;
    public $table;

    public function __construct(){
        $this->model = new ProductClaim;
        $this->table = DB::table('product_claims');
    }

    public function all()
    {
        //$qr = "SELECT DISTINCT user_uuid
        //FROM product_claims";

        //return DB::select($qr);//->paginate(20);

        return $this->table->select(['users.first_name','users.last_name','users.username',
        'packages.name AS package_name','product_claims.status','product_claims.id','product_claims.created_at','product_claims.user_uuid'])
        //->join('products', 'products.id', '=', 'product_claims.product_id')
        ->join('users','users.uuid','=','product_claims.user_uuid')
        ->join('packages','packages.id', '=','users.package_id')
        
        //->select(['users.first_name','users.last_name','users.username',
        //'packages.name AS package_name','product_claims.status','product_claims.id','product_claims.created_at','product_claims.user_uuid'])
        ->whereIn('status',['processing'])
        ->paginate(20);
    }

    public function create(array $data)
    {
        return (new ProductClaim($data))->save(); //$this->model->save($data);
    }

    //approve or disapprove
    public function update(string $uuid, array $data)
    {
        // info('hu');
        // $status = $data['status'];
        // $query = "UPDATE product_claims SET status = '$status' WHERE user_uuid = '$uuid'";
        // DB::select($query);
        return $this->table->where('user_uuid', $uuid)->update($data);
    }

    //public function

    public function claimedProducts(string $user_uuid)
    {
        return $this->table->leftJoin('products','products.id','=','product_claims.product_id')
        ->where('user_uuid',$user_uuid)->get(['product_claims.created_at','products.name','product_claims.id',
        'product_claims.user_uuid','products.worth','products.points','product_claims.status']);
    }

}