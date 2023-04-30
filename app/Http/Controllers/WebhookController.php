<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WebhookService;

class WebhookController extends Controller
{
    private $service;
    public function __construct()
    {
        $this->service = new WebhookService;
    }

    public function processPaystackWebhook(Request $request)
    {
        $data = $request->all();
        if($request->header('x-paystack-signature') !== hash_hmac('sha512', $data, env('PAYSTACK_AUTH_KEY'))){
            return response()->json(['message'=>'Unauthorized request'],401);
        }

        $provider = 'paystack';
        $res = $this->service->process($provider,$data);
        return response()->json($res,$res['status']);
    }

    public function processFincraWebhook(Request $request)
    {
        $data = $request->all();
        if($request->header('x-paystack-signature') !== hash_hmac('sha512', $data, env('PAYSTACK_AUTH_KEY'))){
            return response()->json(['message'=>'Unauthorized request'],401);
        }

        $provider = 'paystack';
        $res = $this->service->process($provider,$data);
        return response()->json($res,$res['status']);
    }
}
