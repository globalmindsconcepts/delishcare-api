<?php
namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Services\Paystack\PaystackWebhookService;
use App\Repository\WebhookLogRepository;

class WebhookService extends BaseService{

    private $webhookRepository;

    function __construct()
    {
        $this->webhookRepository = new WebhookLogRepository;
    }

    public function process($provider,$data)
    {
        switch ($provider) {
                
            case 'paystack':
                try {
                    $event = $data['event'];
                    $payload = $data['data'];

                    $logData['provider'] = 'paystack';
                    $logData['provider_reference'] = $payload['reference'] ?? $payload['id'];

                    if($wbk = $this->checkProcessedWebhook($provider,$logData['provider_reference'])){
                        if($wbk['processed'] == 1){
                            return ["success"=>true,"message"=>"Webhook already processed","status"=>200];
                        }
                    }

                    $logData['request_data'] = json_encode($data);
                    $logData['status'] = 'processing';
                    $logData['event_type'] = $event;

                    $this->logWebhookRequest($logData);
                    
                    (new PaystackWebhookService)->process($event,$payload,$logData['provider_reference']);

                    return ["success"=>true,"message"=>"Webhook processed successfuly","status"=>200];
                } catch (Exception $e) {
                    return $this->logger($e,"Error processing paystack webhook");
                }
                break;
            
            default:
                return ["success"=>false,"message"=>"Error processing webhook, provider not matched","status"=>400];
                break;
        }
    }

    public function logWebhookRequest($data)
    {
        $this->webhookRepository->create($data);
    }

    public function checkProcessedWebhook($provider,$reference)
    {
      return $this->webhookRepository->getWebhookData($provider,$reference);
    }
}
