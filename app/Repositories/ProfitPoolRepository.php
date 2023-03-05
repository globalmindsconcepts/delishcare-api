<?php
namespace App\Repositories;

use App\Models\ProfitPool;
use Illuminate\Support\Facades\DB;

class ProfitPoolRepository{

    private $model;
    public $table;

    public function __construct(){
        $this->model = new ProfitPool;
        $this->table = DB::table('profit_pools');
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