<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Product::first()){
            (new Product([
                'name'=>'1KG DELIDH CHOCO',
                'points'=>3.5,
                'worth'=>12250
            ]))->save();
    
            (new Product([
                'name'=>'500GM',
                'points'=>2,
                'worth'=>7000
            ]))->save();
    
            (new Product([
                'name'=>'DELIDH HEALTHY CHOCOLATE BARS',
                'points'=>1.25,
                'worth'=>4373
            ]))->save();
    
            (new Product([
                'name'=>'OMA HEALTHY LIQUID PEPPER',
                'points'=>1.5,
                'worth'=>5200
            ]))->save();
        }
    }
}
