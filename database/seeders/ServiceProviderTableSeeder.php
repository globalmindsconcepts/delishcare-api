<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceProvider;

class ServiceProviderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!ServiceProvider::first()){
            (new ServiceProvider([
                'name'=>'paystack'
            ]))->save();

            (new ServiceProvider([
                'name'=>'fincra'
            ]))->save();
        }
    }
}
