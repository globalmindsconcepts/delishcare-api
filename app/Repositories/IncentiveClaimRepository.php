<?php
namespace App\Repositories;

use App\Models\IncentiveClaim;
use Illuminate\Support\Facades\DB;
use \Exception;

class IncentiveClaimRepository{

    private $model;
    public $table;

    public function __construct(){
        $this->model = new IncentiveClaim;
        $this->table = DB::table('incentive_claims');
    }

    public function all()
    {
        return $this->table->leftJoin('incentives', 'incentives.id', '=', 'incentive_claims.incentive_id')
        ->leftJoin('users','users.uuid','=','incentive_claims.user_uuid')
        ->leftJoin('packages','packages.id','=','users.package_id')
        ->leftJoin('ranks','ranks.id','=','users.rank_id')
        ->where('incentive_claims.status','=','processing')
        ->select(['incentives.incentive','incentives.worth','incentive_claims.status','incentive_claims.id','incentive_claims.created_at',
        'users.username','users.first_name','users.last_name','packages.name AS package_name','ranks.name AS rank_name','ranks.points'])
        ->paginate(20);
    }

    public function create(array $data)
    {
        return (new IncentiveClaim($data))->save(); //$this->model->save($data);
    }

    public function update(int $id, array $data)
    {
        return $this->table->where('id', '=', $id)->update($data);
    }

    public function claimedIncentives(string $user_uuid)
    {
        return $this->table->leftJoin('incentives','incentives.id','=','incentive_claims.incentive_id')
        ->leftJoin('ranks','ranks.id','=','incentives.rank_id')->where('incentive_claims.user_uuid', '=', $user_uuid)->get(['ranks.points','incentives.incentive','incentive_claims.created_at']);
    }

    public function checkClaimedIncentive(string $uuid, int $rank_id)
    {
        return $this->table->where('user_uuid',$uuid)->where('rank_id',$rank_id)->get()->first();
    }

}