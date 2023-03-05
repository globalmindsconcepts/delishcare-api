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
        return $this->table->leftJoin('incentives', 'incentives.id', '=', 'incentive_claims.incentive_id')->paginate(20);
    }

    public function create(array $data)
    {
       return $this->model->save($data);
    }

    public function update(int $id, array $data)
    {
        return $this->table->where('id', '=', $id)->update($data);
    }

}