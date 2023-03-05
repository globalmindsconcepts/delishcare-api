<?php
namespace App\Repositories;

use App\Models\WelcomeBonus;
use Illuminate\Support\Facades\DB;

class WelcomeBonusRepository{

    private $model;
    public $table;
    private $user; //= User::class;

    public function __construct(){
        $this->model = new WelcomeBonus;
        $this->table = DB::table('welcome_bonuses');
        //$this->user = UserProfile::class;
        
    }

    public function create(array $data)
    {
       return (new WelcomeBonus($data))->save(); //$this->table->insert($data);
    }

    public function update(string $uuid, array $data)
    {
        return $this->table->where('user_uuid', $uuid)->update($data);
    }

}