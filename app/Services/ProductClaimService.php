<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\ProductClaimRepository;
use App\Repositories\ProductRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class ProductClaimService extends BaseService{
    private $productClaimRepo,$productRepo;
    public function __construct(){
        $this->productClaimRepo = new productClaimRepository;
        $this->productRepo = new ProductRepository;
    }

    public function all()
    {
        try {
            $products = $this->productClaimRepo->all();
            return ['data' => $products, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            return $this->logger($e,"error fetching product claims");
        }
    }

    public function create(string $uuid, array $product_ids)
    {
        try {
            $count = $this->productClaimRepo->table->where('user_uuid',$uuid)->whereIn('status',['processing','approved'])->count();
            if($count > 0){
                return ['message'=>'You have already claimed products','status'=>400];
            }

            $data['user_uuid'] = $uuid;
            $totalWorth=0;
            foreach($product_ids as $val){
                //info($val,[$this->productRepo->get($val)]);
                $totalWorth = $totalWorth + ($this->productRepo->get($val)->worth);
            }

            $user = User::where('uuid',$uuid)->first();
            if($user->packagePayment->first()->amount/2 < $totalWorth){
                //info($totalPoints);
                return ['message'=>'Insufficient package amount, please select items with lesser prices','status'=>400];
            }

            foreach($product_ids as $val){
                $data['product_id'] = $val;
                $data['points'] = $this->productRepo->get($val)->points;
                $this->productClaimRepo->create($data);
            }

            $selectedProducts = $this->productClaimRepo->claimedProducts($uuid);//->where('user_uuid',$uuid)->get();
            return ['data' => $selectedProducts, 'message' => 'product claimed successfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"error creating product claim");
        }
    }

    public function update(string $uuid, array $data)
    {
        try {
            $this->productClaimRepo->update($uuid, $data);
            return ['message' => 'product claim updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"error updating product claim");
        }
    }

    public function claimedProducts(string $user_uuid)
    {
        try {
            $data = $this->productClaimRepo->claimedProducts($user_uuid);
            return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"error fetching claimed products");
        }
    }

    public function sumClaimedProducts()
    {
        try {
            $data = $this->productClaimRepo->table
            ->leftJoin('products','products.id','=','product_claims.product_id')
            ->where('status','approved')->get()->sum('worth');
            return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"error summing claimed products");
        }
    }

    public function totalProductSold()
    {
        try {
            $data = $this->productClaimRepo->table->where('status','approved')->get()->count();
            return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"error fetching total products sold");
        }
    }

    public function totalProductPV()
    {
        try {
            $data = $this->productClaimRepo->table->where('status','approved')->get()->sum('points');
            return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"error fetching total products pv");
        }
    }
}