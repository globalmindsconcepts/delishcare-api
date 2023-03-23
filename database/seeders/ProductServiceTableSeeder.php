<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductService;

class ProductServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!ProductService::first()){
            (new ProductService([
                'service'=>'payment',
                'default_provider_id'=>1
            ]))->save();

            (new ProductService([
                'service'=>'payout',
                'default_provider_id'=>2
            ]))->save();
        }
        
    }
}
