<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Admin::first()){
            (new Admin([
                'name'=>'admin',
                'email'=>'admin@delishcare.com',
                'email_verified_at'=> now(),
                'is_admin'=>true,
                'password'=>'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            ]))->save();
        }
        
    }
}
