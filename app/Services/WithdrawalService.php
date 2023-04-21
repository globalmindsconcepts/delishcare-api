<?php
namespace App\Services;

use App\Repositories\WithdrawalRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class WithdrawalService extends BaseService{

    private $repo;
    private $service;
    public function __construct(){
        $this->service = $this->getService(env('DEFAULT_PAYOUT_PROCESSOR','fincra'));
        $this->repo = new WithdrawalRepository;
    }

    public function all()
    {
        try {
            $data = $this->repo->all();
            return ['data' => $data, 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"withdrawal history error");
        }
    }

    public function userHistory(string $uuid)
    {
        try {
            $data = $this->repo->userHistory($uuid);
            return ['data' => $data, 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"user withdrawal history error");
        }
    }

    public function details(Int $id)
    {
        try {
            $data = $this->repo->details($id);
            return ['data' => $data, 'message' => 'withdrawal fetched successfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"fetch withdrawal error");
        }
    }

    public function create(string $uuid, array $data)
    {
        try {
            //check balance with provider 

            if($this->repo->processingWithdrawal($uuid) > 0){
                return ['message'=>'You have a pending withdrawal','status'=>400];
            }

            //check user balance
            if($data['amount'] > (new WalletService)->totalBalance($uuid)){
                return ['message'=>'Insuffucuent funds','status'=>400];
            }

            $data['status'] = 'processing';
            $data['reference']=Str::random(10);
           $withdrawal = $this->repo->create($data+['user_uuid'=>$uuid]);
            return ['data' => $withdrawal, 'message' => 'Withdrawal created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"create withdrawal error");
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $this->repo->update($id, $data);
            return ['message' => 'Withdrawal updated succesfully', 'success'=>true, 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"update rank error");
        }
    }

    public function total()
    {
        try {
            $total = $this->repo->total(); 
            return ['data' => $total, 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"total withdrawal error");
        }
    }

    public function userTotal(string $uuid)
    {
        try {
            $total = $this->repo->userTotal($uuid);
            return ['data' => $total, 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"user total withdrawal error");
        }
    }

    public function providerBalanceCheck()
    {
        try {
            //$total = $this->repo->userTotal($uuid);
            //return ['data' => $total, 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"user total withdrawal error");
        }
    }

    private function getService($provider)
    {
        $service = (new \App\Repositories\ProductServiceRepository)->getService('payout');
        if( $service && array_key_exists('default_provider_id',$service)){
            $defaultProvider = (new \App\Repositories\ServiceProviderRepository)->getProviderById($service['default_provider_id']);
            $provider = $defaultProvider ? $defaultProvider['name'] : $provider;
        }
        switch ($provider) {
            case 'paystack':
                //$service = (new PaystackPaymentService);
                break;
            
            default:
                //return $service = new PaystackPaymentService;
                break;
        }

        return $service;
    }
}