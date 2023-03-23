<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class SingleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!User::first()){
            User::factory([
                'first_name'=>'Delishcare',
                'last_name'=>'Delsihcare',
                'email'=>'delishcare@mail.com',
                'username'=>'delishcare',
                'package_id'=>1
            ])->create();
        }
    }
}
