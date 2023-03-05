<?php
namespace App\Repositories;

use App\Models\ReferralBonus;
use Illuminate\Support\Facades\DB;

class ReferralBonusRepository{

    private $model;
    public $table;

    public function __construct(){
        $this->model = new ReferralBonus;
        $this->table = DB::table('referral_bonuses');
    }

    public function create(array $data)
    {
       return $this->table->insert($data);
    }

    public function update(string $uuid, array $data)
    {
        return $this->table->where('user_uuid', $uuid)->update($data);
    }

}