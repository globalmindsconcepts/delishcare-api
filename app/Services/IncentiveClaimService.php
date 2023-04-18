<?php
namespace App\Services;

use App\Repositories\IncentiveClaimRepository;
use App\Repositories\IncentiveRepository;
use App\Repositories\UserRepository;
use App\Repositories\RankRepository;

use \Exception;
use Illuminate\Support\Facades\Log;
class IncentiveClaimService{
    private $incentiveClaimRepo,$incentiveRepo, $userRepo,$rankRepo;
    public function __construct(){
        $this->incentiveClaimRepo = new IncentiveClaimRepository;
        $this->incentiveRepo = new IncentiveRepository;
        $this->userRepo = new UserRepository;
        $this->rankRepo = new RankRepository;
    }

    public function all()
    {
        try {
            $incentives = $this->incentiveClaimRepo->all();
            return ['data' => $incentives, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            Log::error("error fetching incentive claims", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500, 'success'=>false];
        }
    }

    public function create(string $uuid, array $data)
    {
        try {
            if($this->incentiveClaimRepo->table->where('incentive_id',$data['incentive_id'])->where('user_uuid',$uuid)->count() > 0){
                return ['message'=>'You have already claimed this incentive',400];
            }

            if($this->incentiveClaimRepo->table->where('status','processing')->where('user_uuid',$uuid)->count() > 0){
                return ['message'=>'You already have a pending incentive',400];
            }
            $data['user_uuid'] = $uuid;
           $incentive = $this->incentiveClaimRepo->create($data);
            return ['data' => $incentive, 'message' => 'Incentive claimed succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error("error creating incentive claim", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return [ 'message' => $message, 'status' => 500];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $this->incentiveClaimRepo->update($id, $data);
            return ['message' => 'Incentive claim updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error("error updating incentive claim", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function claimedIncentives(string $user_uuid)
    {
        try {
            $data = $this->incentiveClaimRepo->claimedIncentives($user_uuid);
            return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            Log::error("error fetching claimed incentives", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function currentIncentive(string $uuid)
    {
        try {
           $user = $this->userRepo->getUser($uuid);
           $incentives = $this->incentiveRepo->table->get();//->toArray();
           $incentive = null;
           //info('user-rank',[$user['rank_id']]);
           foreach ($incentives as $value) {
            //info('rank',[$value->rank_id]);
                if($value->rank_id == $user['rank_id']){
                    $incentive = $value;
                    $claim = $this->incentiveClaimRepo->checkClaimedIncentive($uuid,$user['rank_id']);
                    if($claim){
                        $incentive->claim_status = $claim->status;
                    }else{
                        $incentive->claim_status = null;
                    }
                    break;
                }
           }
           return ['data'=>$incentive, 'status'=>200];
        } catch (Exception $e) {
            Log::error("error fetching current incentive", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}