<?php
namespace App\Repositories;

use App\Models\ReferralBonusSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReferralBonusSettingRepository{
    private $model;
    public $table;
    private $package;
    public function __construct(){
        $this->model = new ReferralBonusSetting;
        $this->table = DB::table('referral_bonus_settings');
        //$this->package = new Model($this->model)
    }


    public function create(array $data)
    {
        return $this->table->insert($data);
    }

    public function update(Int $id, array $data)
    {
        return $this->table->where(['id'=>$id])->update($data);
    }

    public function get($package_id)
    {
       return $this->table->where('package_id','=',$package_id)->first();
    }

    public function all()
    {
       return $this->table->leftJoin('packages','packages.id','=','referral_bonus_settings.package_id')->get();
    }



}