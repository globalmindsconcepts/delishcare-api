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

    public function refereds(string $user_uuid, $placement=false)
    {
       $qury = \App\Models\Referral::where('referrer_uuid',$user_uuid);//->get();
       if($placement==true){
          return  $qury->orWhere('placer_uuid',$user_uuid)->get();
       }
       return $qury->get();
    }

}