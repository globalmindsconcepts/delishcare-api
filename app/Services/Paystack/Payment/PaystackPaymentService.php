<?php
namespace App\Services\Paystack\Payment;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\Paystack\Payment\PaystackPaymentMockedResponse;

const INITIATE_ENDPOINT = 'transaction/initialize';
const VERIFY_ENDPOINT = 'transaction/verify'; //transaction/verify/${reference}

class PaystackPaymentService{

    use PaystackPaymentMockedResponse;

    private $authKey;
    private $baseUrl;
    function __construct()
    {
        $this->authKey =  env('PAYSTACK_AUTH_KEY');
        $this->baseUrl =  env('PAYSTACK_BASE_URL');
    }

    /**
     * initiate payment
     * data - [email,amount]
     * https://paystack.com/docs/api/#transaction-initialize
     */
    public function initiate($data)
    {
        if(env('APP_ENV') == 'testing'){

            // return '{
            //     "status": true,
            //     "message": "Authorization URL created",
            //     "data": {
            //       "authorization_url": "https://checkout.paystack.com/0peioxfhpn",
            //       "access_code": "0peioxfhpn",
            //       "reference": "'.$data['reference'].'"
            //     }
            //   }';
            return $this->mockInitiatePaymentResponse($data['reference']);
        }

        $data['amount'] = $data['converted_amount'] * 100;
        $data['email'] = auth()->user()->email;
        $data['currency'] = $data['charge_currency'];
        $data['description'] = '80Leaves wallet funding';
        //$data['reference'] = 
        //info([$data]);
        
        $response = Http::withHeaders([
            'Accept'=>'application/json',
            'Content-Type' => 'application/json',
            "Authorization"=> "Bearer $this->authKey"
        ])->post($this->baseUrl.'/'.INITIATE_ENDPOINT,$data);
            
        Log::info("Initiate payment response",[$response]);

        $response->throw();
        
        return $response->body();
    }

    /**
     * https://paystack.com/docs/api/#transaction-verify
     * verify payment
     * data = ['reference']
     */
    public function verify(array $data)
    {
        if(env('APP_ENV')=='testing'){
            return $this->mockVerifyPaymentResponse();
        }

        $reference = $data['reference'];

        $response = Http::withHeaders([
            'Accept'=>'application/json',
            'Content-Type' => 'application/json',
            "Authorization"=> "Bearer {$this->authKey}"
        ])->get($this->baseUrl.'/'.VERIFY_ENDPOINT.'/'.$reference);
            
        Log::info("verify payment response",[$response]);

        $response->throw();
        
        return $response->body();

    }

}