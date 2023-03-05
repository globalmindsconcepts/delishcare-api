<?php
namespace App\Services;

use App\Repositories\IncentiveRepository;
use \Exception;
class IncentiveService{
    private $incentiveRepo;
    public function __construct(){
        $this->incentiveRepo = new IncentiveRepository;
    }

    public function all()
    {
        try {
            $incentives = $this->incentiveRepo->all();
            return ['data' => $incentives, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500, 'success'=>false];
        }
    }

    public function create(array $data)
    {
        try {
           $incentive = $this->incentiveRepo->create($data);
            return ['data' => $incentive, 'message' => 'Incentive created succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['data' => $incentive, 'message' => $message, 'status' => 500];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $this->incentiveRepo->update($id, $data);
            return ['message' => 'Incentive updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function delete(int $id)
    {
        try {
            $this->incentiveRepo->table->delete($id);
            return ['message' => 'Incentive deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}