<?php
namespace App\Repositories;

use App\Models\LoyaltyBonus;
use Illuminate\Support\Facades\DB;

class LoyaltyBonusRepository{

    private $model;
    public $table;

    public function __construct(){
        $this->model = new LoyaltyBonus;
        $this->table = DB::table('loyalty_bonuses'); 
    }

    public function create(array $data)
    {
       return (new LoyaltyBonus($data))->save(); //$this->model->save($data);
    }

    public function update(string $uuid, array $data)
    {
        return $this->table->where('user_uuid', $uuid)->update($data);
    }

}