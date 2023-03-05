<?php
namespace App\Services;

use App\Services\Paystack\Payment\PaystackPaymentService;
use App\Repositories\TransactionRepository;
use App\Repositories\PackagePaymentRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Repositories\UserRepository;
use App\Repositories\PackageRepository;
use App\Repositories\ReferralRepository;
use App\Repositories\WelcomeBonusRepository;

class PaymentService{
    //use WalletAccountHelpers;

    private $service;
    private $transactionRepository;
    private $packagePayment,$package,$user,$referral,$welcomeBonus;

    function __construct()
    {
        $this->service = $this->getService(env('DEFAULT_PAYMENT_PROCESSOR','paystack'));
        $this->transactionRepository = new TransactionRepository;
        $this->packagePayment = new PackagePaymentRepository;
        $this->package = new PackageRepository;
        $this->user = new UserRepository;
        $this->referral = new ReferralRepository;
    }

    public function initiate(string $user_uuid, array $data)
    {
        try {
            $data['reference'] = $this->generateTransactionReference();
            $data['description'] = 'package payment';
            
            $initPay = $this->service->initiate($data);
            $fundData['user_uuid'] = $user_uuid;
            $fundData['amount'] = $data['amount'];// + $fee;//calculate fee
            $fundData['narration'] = $data['description'];
            $fundData['txn_source'] = 'package_payment';
            $fundData['txn_type'] = 'credit';
            $fundData['txn_reference'] = $data['reference'];
            $fundData['txn_status'] = 'processing';
            $fundData['fee'] = 0;
            $fundData['currency'] = 'NGN';
            $fundData['source_reference'] = '';
            $fundData['processor'] = $data['processor'] ?? '';

            $this->transactionRepository->create($fundData);

        } catch (Exception $e) {
            Log::error("Error initiating payment",[$e]);
            return ["success"=>false,"message"=>$e->getMessage(),"status"=>500];
        }
       
        return ["success"=>true, "data"=>json_decode($initPay,true),
        "message"=>"Payment initiated sucessfully","status"=>200];
    }

    public function verify($data)
    {
        try {
            $this->service->verify($data);
            $transaction = $this->transactionRepository->getReference($data['reference']);
            if(! $transaction){ 
                throw new Exception("Transaction reference does not exist");
            }

            $this->transactionRepository->table->where('txn_reference','=',$data['reference'])->update(['txn_status'=>'successful']);

            $user = $this->user->getUser($transaction['user_uuid']);
            if($user){
                $package_pv = $this->package->get($user['package_id'])->point_value;
                //info('pak', [$package_pv]);
                $paymentData = [
                    'user_uuid'=>$transaction['user_uuid'],
                    'reference'=>$transaction['txn_reference'],
                    'amount'=>$transaction['amount'],
                    'status'=>'approved',
                    'processor'=>$transaction['processor'],
                    'point_value'=>$package_pv
                ];

                $this->packagePayment->create($paymentData);

                (new WalletService)->computeWelcomeBonus($transaction['user_uuid']);

                $ref = $this->referral->get($transaction['user_uuid']);
                
                (new GenealogyService)->makeReferrerAParent($ref->referrer_uuid, $ref->referred_uuid, $ref->placer_uuid);
            }
            
         } catch (Exception $e) {
             Log::error("Error verifying payment",[$e]);
             return ["success"=>false,"message"=>$e->getMessage(),"status"=>500];
         }

        return ["success"=>true, "data"=>[],
        "message"=>"Payment verified successfully","status"=>200];
    }

    public function processChargeSuccessWebhook($data)
    {
        $verify = $this->verify($data);
       if($verify['success']==false){
            throw new Exception("Error while processing charge success webhook");
       }
    }

    private function getService($provider)
    {
        $service = (new \App\Repositories\ProductServiceRepository)->getService('payment');
        if( $service && array_key_exists('default_provider_id',$service)){
            $defaultProvider = (new \App\Repositories\ServiceProviderRepository)->getProviderById($service['default_provider_id']);
            $provider = $defaultProvider ? $defaultProvider['name'] : $provider;
        }
        switch ($provider) {
            case 'paystack':
                $service = (new PaystackPaymentService);
                break;
            
            default:
                return $service = new PaystackPaymentService;
                break;
        }

        return $service;
    }

    private function generateTransactionReference()
    {
        return Str::random(16);
    }

}
