<?php
namespace App\Repositories;

use App\Models\EquilibrumBonus;
use Illuminate\Support\Facades\DB;

class EquilibrumBonusRepository{

    private $model;
    public $table;

    public function __construct(){
        $this->model = new EquilibrumBonus;
        $this->table = DB::table('equilibrum_bonuses'); 
    }

    public function create(array $data)
    {
       return (new EquilibrumBonus($data))->save(); //$this->model->save($data);
    }

    public function update(string $uuid, array $data)
    {
        return $this->table->where('user_uuid', $uuid)->update($data);
    }

}