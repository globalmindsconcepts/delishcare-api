<?php
namespace App\Repositories;

use App\Models\PlacementBonus;
use Illuminate\Support\Facades\DB;

class PlacementBonusRepository{

    private $model;
    public $table;
    private $user; //= User::class;

    public function __construct(){
        $this->model = new PlacementBonus;
        $this->table = DB::table('placement_bonuses');
        //$this->user = UserProfile::class;
        
    }

    public function create(array $data)
    {
       return $this->table->insert($data);
        //$this->user::find()
    }

    public function update(string $uuid, array $data)
    {
        return $this->table->where('user_uuid', $uuid)->update($data);
    }

}