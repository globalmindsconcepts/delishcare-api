<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\ProductClaimRepository;
use App\Repositories\ProductRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class ProductClaimService{
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
            Log::error("error fetching product claims", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500, 'success'=>false];
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
            $totalPoints=0;
            foreach($product_ids as $val){
                //info($val,[$this->productRepo->get($val)]);
                $totalPoints = $totalPoints + $this->productRepo->get($val)->points;
            }

            $user = User::where('uuid',$uuid)->first();
            if($user->packagePayment->first()->point_value < $totalPoints){
                info($totalPoints);
                return ['message'=>'Insufficient point values','status'=>400];
            }

            foreach($product_ids as $val){
                $data['product_id'] = $val;
                $data['points'] = $this->productRepo->get($val)->points;
                $this->productClaimRepo->create($data);
            }

            $selectedProducts = $this->productClaimRepo->claimedProducts($uuid);//->where('user_uuid',$uuid)->get();
            return ['data' => $selectedProducts, 'message' => 'product claimed successfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error("error creating product claim", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return [ 'message' => $message, 'status' => 500];
        }
    }

    public function update(string $uuid, array $data)
    {
        try {
            $this->productClaimRepo->update($uuid, $data);
            return ['message' => 'product claim updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error("error updating product claim", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function claimedProducts(string $user_uuid)
    {
        try {
            $data = $this->productClaimRepo->claimedProducts($user_uuid);
            return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            Log::error("error fetching claimed products", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
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
            Log::error("error summing claimed products", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function totalProductSold()
    {
        try {
            $data = $this->productClaimRepo->table->where('status','approved')->get()->count();
            return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            Log::error("error fetching total products sold", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function totalProductPV()
    {
        try {
            $data = $this->productClaimRepo->table->where('status','approved')->get()->sum('points');
            return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            Log::error("error fetching total products pv", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}