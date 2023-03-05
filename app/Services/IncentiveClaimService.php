<?php
namespace App\Services;

use App\Repositories\IncentiveClaimRepository;
use \Exception;
class IncentiveClaimService{
    private $incentiveClaimRepo;
    public function __construct(){
        $this->incentiveClaimRepo = new IncentiveClaimRepository;
    }

    public function all()
    {
        try {
            $incentives = $this->incentiveClaimRepo->all();
            return ['data' => $incentives, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500, 'success'=>false];
        }
    }

    public function create(array $data)
    {
        try {
           $incentive = $this->incentiveClaimRepo->create($data);
            return ['data' => $incentive, 'message' => 'Incentive claimed succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['data' => $incentive, 'message' => $message, 'status' => 500];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $this->incentiveClaimRepo->update($id, $data);
            return ['message' => 'Incentive claim updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}