<?php
namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;


class SettingRepository{
    private $model;
    private $table;
    private $user;
    public function __construct(){
        $this->model = new Setting;
        $this->table = DB::table('settings');
        //$this->user = Transaction::class;
    }

    public function get(string $param)
    {
        return $this->table->first($param);
    }

    public function getSettings()
    {
        return $this->table->first();
    }


    public function create(array $data)
    {
        return $this->model->save($data);
    }

    public function update(array $data)
    {
        $this->table->where('id','=', 1)->update($data);
    }

}