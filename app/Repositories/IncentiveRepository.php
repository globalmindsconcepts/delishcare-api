<?php
namespace App\Repositories;

use App\Models\Incentive;
use Illuminate\Support\Facades\DB;
use \Exception;

class IncentiveRepository{

    private $model;
    public $table;
    private $user; //= User::class;

    public function __construct(){
        $this->model = new Incentive;
        $this->table = DB::table('incentives');
    }

    public function all()
    {
        return $this->table->leftJoin('ranks', 'incentives.rank_id', '=', 'ranks.id')
        ->select(['ranks.name','ranks.points','incentives.worth','incentives.incentive','incentives.file_path','incentives.id','incentives.rank_id'])
        ->paginate(20);
    }

    public function get($id)
    {
        return $this->table->leftJoin('ranks','ranks.id','=','incentives.rank_id')->where('ranks.id', $id)->get()->first();
    }

    public function create(array $data)
    {
        return (new Incentive($data))->save(); //$this->model->save($data);
    }

    public function update(int $id, array $data)
    {
        return $this->table->where('id', '=', $id)->update($data);
    }

}