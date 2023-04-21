<?php
namespace App\Services;

use App\Repositories\ProfitPoolRepository;
use \Exception;
class ProfitPoolService extends BaseService{

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
            return $this->logger($e,"Error fetching all profit pools");
        }
    }

    public function create(array $data)
    {
        try {
           $rank = $this->profitPoolRepo->create($data);
            return ['data' => $rank, 'message' => 'Profit pool created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error creating profit pool");
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $this->profitPoolRepo->update($id, $data);
            return ['message' => 'Profit pool updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error updating profit pool");
        }
    }

    public function delete(int $id)
    {
        try {
            $this->profitPoolRepo->table->delete($id);
            return ['message' => 'Profit pool deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error deleting profit pool");
        }
    }
}