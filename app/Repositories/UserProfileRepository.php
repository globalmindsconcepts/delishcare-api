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
       return (new UserProfile($data))->save(); //$this->model->save($data);
    }

    public function update(string $uuid, array $data)
    {
        if($this->table->where('user_uuid', $uuid)->exists()){
            return $this->table->where('user_uuid', $uuid)->update($data);
        }
        return $this->create($data+['user_uuid'=>$uuid]);
        
    }

    public function get(string $user_uuid)
    {
        return $this->table->where('user_uuid',$user_uuid)->first();
    }

    public function updateBankDetails(string $uuid, array $data)
    {

        //return $this->table->where('user_uuid', $uuid)->updatesert($data);
    }

    public function toggle2FA(string $uuid,$data)
    {
        return $this->table->where('user_uuid',$uuid)->update($data);
    }

    public function toggleBankEditable(string $uuid,$bank_editable)
    {
        return $this->table->where('user_uuid',$uuid)->update(['bank_editable'=>$bank_editable]);
    }

}