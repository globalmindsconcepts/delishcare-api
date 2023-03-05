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
        return $this->table->leftJoin('ranks', 'incentives.rank_id', '=', 'ranks.id')->paginate(20);
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