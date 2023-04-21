<?php
namespace App\Services;

use App\Repositories\IncentiveRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class IncentiveService extends BaseService{
    private $incentiveRepo;
    public function __construct(){
        $this->incentiveRepo = new IncentiveRepository;
    }

    public function all()
    {
        try {
            $incentives = $this->incentiveRepo->all();
            //info('inc',[$incentives]);
            return ['data' => $incentives, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching all incentives");
        }
    }

    public function get($id)
    {
        try {
            $incentive = $this->incentiveRepo->get($id);
            return ['data' => $incentive, 'message' => 'Incentive fetched succesfully', 'success'=>true, 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"fetch incentive error");
        }
    }

    public function create(array $data)
    {
        try {
           $incentive = $this->incentiveRepo->create($data);
            return ['data' => $incentive, 'message' => 'Incentive created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"create incdntive error");
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $this->incentiveRepo->update($id, $data);
            return ['message' => 'Incentive updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"update incdntive error");
        }
    }

    public function delete(int $id)
    {
        try {
            $this->incentiveRepo->table->delete($id);
            return ['message' => 'Incentive deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"delete incdntive error");
        }
    }
}