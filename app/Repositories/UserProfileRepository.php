<?php
namespace App\Repositories;

use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;

class UserProfileRepository{

    private $model;
    private $table;
    private $user; //= User::class;

    public function __construct(){
        $this->model = new UserProfile;
        $this->table = DB::table('user_profiles');
        //$this->user = UserProfile::class;
        
    }

    public function create(array $data)
    {
       return $this->model->save($data);
    }

    public function update(string $uuid, array $data)
    {
        return $this->table->where('user_uuid', $uuid)->update($data);
    }

}