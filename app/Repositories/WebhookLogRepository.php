<?php
namespace App\Repository;

use Illuminate\Support\Facades\DB;

class WebhookLogRepository {

    public function create(array $data)
    {
        $sql = "INSERT INTO webhook_logs (provider_reference,event_type,request_data,status,provider,created_at) 
        values(?,?,?,?,?,?)";
        $res = DB::insert($sql,[$data['provider_reference'],$data['event_type'],
        $data['request_data'],$data['status'],$data['provider'],now()]);
        return $res;
    }

    public function update(string $refernce, $data)
    {
        $date = now();
        $processed = $data['processed'];
        $status = $data['status'];
        $resData = $data['response_data'];
        $source = $data['source'];
        $sql = "UPDATE webhook_logs SET processed = $processed, response_data = '$resData', status='$status', updated_at = '$date', source='$source' 
        WHERE provider_reference = '$refernce'";
        $res = DB::update($sql);
        return $res;
    }

    public function getWebhookData($provider, $provider_reference)
    {
        $sql = "SELECT * FROM webhook_logs WHERE provider = '$provider' 
        AND provider_reference = '$provider_reference' LIMIT 1";
        $res = DB::select($sql);
        if(empty($res[0])){
            return null;
        }
        return (array)$res[0];
    }
}