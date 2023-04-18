<?php
namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;


class TransactionRepository{
    private $model;
    public $table;
    public function __construct(){
        $this->model = new Transaction;
        $this->table = DB::table('transactions');
    }


    public function create(array $data)
    {
        return (new Transaction($data))->save(); //$this->table->insert($data);
    }

    public function update(string $reference, array $data)
    {
        return $this->table->where('txn_reference', $reference)->update($data);
    }

    public function getReference($txn_reference)
    {
        $sql = "SELECT * FROM transactions
        WHERE txn_reference = '$txn_reference' LIMIT 1";
        $res = DB::select($sql);
        $data = (array)$res;
        if(!empty($data)){
            return (array)$data[0];
        }
        return null;
    }

}