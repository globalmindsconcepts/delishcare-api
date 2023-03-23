<?php
namespace App\Repositories;

use App\Models\Rank;
use Illuminate\Support\Facades\DB;

class RankRepository{

    private $model;
    public $table;
    private $user; //= User::class;

    public function __construct(){
        $this->model = new Rank;
        $this->table = DB::table('ranks');
        //$this->user = UserProfile::class;
        
    }

    public function all()
    {
        return $this->table->paginate(20);
    }

    public function get(Int $id)
    {
        return $this->table->where('id', '=', $id)->first();
    }

    public function create(array $data)
    {
       return (new Rank($data))->save(); //$this->model->save($data);
    }

    public function update($id, array $data)
    {
        return $this->table->where('id', $id)->update($data);
    }

}