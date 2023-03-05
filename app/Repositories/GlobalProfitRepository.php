<?php
namespace App\Repositories;

use App\Models\GlobalProfit;
use Illuminate\Support\Facades\DB;

class GlobalProfitRepository{

    private $model;
    public $table;
    private $user; //= User::class;

    public function __construct(){
        $this->model = new GlobalProfit;
        $this->table = DB::table('global_profits');
        //$this->user = UserProfile::class;
        
    }

    public function create(array $data)
    {
        return (new GlobalProfit($data))->save(); //$this->model->save($data);
    }

    public function update(string $uuid, array $data)
    {
        return $this->table->where('user_uuid', $uuid)->update($data);
    }

}