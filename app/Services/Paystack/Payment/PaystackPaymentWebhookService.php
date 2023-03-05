<?php
namespace App\Services\Paystack\Payment;

use App\Services\PaymentService;

class PaystackPaymentWebhookService{

    private $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService;
    }

    public function processChargeSuccessful($data)
    {
        $this->paymentService->processChargeSuccessWebhook($data);
    }

}