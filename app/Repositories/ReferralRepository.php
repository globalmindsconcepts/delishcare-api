<?php
namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;


class ReferralRepository{
    private $model;
    public $table;
    public function __construct(){
        $this->model = new Transaction;
        $this->table = DB::table('referrals');
    }


    public function create(array $data)
    {
        return $this->table->insert($data);
    }

    public function get(string $user_uuid)
    {
        return $this->table->where('referred_uuid', '=', $user_uuid)->first();
    }

}