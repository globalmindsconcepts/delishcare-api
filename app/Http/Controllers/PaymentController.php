<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PackagePaymentService;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    private $packagePayment;
    private $payment;

    public function __construct(){
        $this->payment = new PaymentService;
    }

    public function initiatePackagePayment(Request $request)
    {
        $user_uuid = $request->user()->uuid;
        $data = $this->payment->initiate($user_uuid, $request->all());
        return response()->json($data, $data['status']);
    }

    public function verifyPackagePayment(Request $request)
    {
        $data = $this->payment->verify($request->all());
        return response()->json($data, $data['status']);
    }

    public function processPayout(Request $request)
    {
        $user_uuid = $request->user()->uuid;
        $data = $this->payment->initiate($user_uuid, $request->all());
        return response()->json($data, $data['status']);
    }
}
