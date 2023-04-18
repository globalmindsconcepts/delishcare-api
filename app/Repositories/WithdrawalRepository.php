<?php
namespace App\Repositories;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class WithdrawalRepository{

    private $model;
    public $table;
    private $user; //= User::class;

    public function __construct(){
        $this->model = new Withdrawal;
        $this->table = DB::table('withdrawals');
    }

    public function all()
    {
        return $this->table->leftJoin('users','users.uuid','=','withdrawals.user_uuid')
        ->select(['users.username','withdrawals.amount','withdrawals.status','withdrawals.created_at'])
        ->paginate(20);
    }

    //user withdrawal history
    public function userHistory(string $uuid)
    {
        return $this->table->where('user_uuid','=',$uuid)->paginate(20);
    }

    public function processingWithdrawal(string $uuid)
    {
        return $this->table->where('user_uuid','=',$uuid)->where('status','=','processing')->count();
    }

    //user total withdraeals
    public function userTotal(string $uuid)
    {
        return $this->table->where('user_uuid','=',$uuid)->where('status','=','successful')->sum('amount');
    }
    //total withdrawals
    public function total()
    {
        return $this->table->where('status','=','successful')->sum('amount');
    }

    public function create(array $data)
    {
       return (new Withdrawal($data))->save();
    }

    public function update(string $uuid, array $data)
    {
        return $this->table->where('user_uuid', $uuid)->update($data);
    }

    public function details(int $id)
    {
        return $this->table->where('id', $id)->get();
    }

}