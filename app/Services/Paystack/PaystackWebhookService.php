<?php
namespace App\Services\Paystack;

use App\Services\Paystack\Payment\PaystackPaymentWebhookService;
use App\Repository\WebhookLogRepository;
use Exception;

class PaystackWebhookService{

    private $webhookRepository;
    private $paymentWebhookService;

    public function __construct()
    {
        $this->webhookRepository = new WebhookLogRepository;
        $this->paymentWebhookService = new PaystackPaymentWebhookService;
    }

    public function process(string $event, array $data, string $provider_reference)
    {
        $logData['status'] = 'successful';
        $logData['processed'] = 1;
        $logData['response_data'] = 'Webhook processed successfully';
        switch ($event) {
            case 'charge.success':
                $this->paymentWebhookService->processChargeSuccessful($data);
                $logData['source'] = 'payment';
                break;
            
            default:
                throw new Exception('Webhook event not found');
                break;
        }

        $this->updateWebhookLog($provider_reference,$logData);
    }

    public function updateWebhookLog($provider_reference,$data)
    {
        $this->webhookRepository->update($provider_reference,$data);
    }
}