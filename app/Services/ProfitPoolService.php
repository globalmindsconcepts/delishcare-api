<?php
namespace App\Services;

use App\Repositories\ProfitPoolRepository;
use \Exception;
class ProfitPoolService{

    private $profitPoolRepo;
    public function __construct(){
        $this->profitPoolRepo = new ProfitPoolRepository;
    }

    public function all()
    {
        try {
            $pools = $this->profitPoolRepo->table->leftJoin('users', 'users.uuid', '=', 'profit_pools.user_uuid')->paginate(20);
            return ['data' => $pools, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function create(array $data)
    {
        try {
           $rank = $this->profitPoolRepo->create($data);
            return ['data' => $rank, 'message' => 'Profit pool created succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $this->profitPoolRepo->update($id, $data);
            return ['message' => 'Profit pool updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function delete(int $id)
    {
        try {
            $this->profitPoolRepo->table->delete($id);
            return ['message' => 'Profit pool deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_env' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}