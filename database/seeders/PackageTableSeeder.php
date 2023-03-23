<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Package::first()){
            (new Package([
                'name'=>'BASIC',
                'vip'=>'vip1',
                'point_value'=>5,
                'registration_value'=>20200,
                'value'=>17500,
                'profit_pool_eligible'=>false,
            ]))->save();
    
            (new Package([
                'name'=>'BUSINESS',
                'vip'=>'vip2',
                'point_value'=>10,
                'registration_value'=>50600,
                'value'=>35000,
                'profit_pool_eligible'=>false,
            ]))->save();
    
            (new Package([
                'name'=>'EXECUTIVE',
                'vip'=>'vip3',
                'point_value'=>20,
                'registration_value'=>102000,
                'value'=>70000,
                'profit_pool_eligible'=>false,
            ]))->save();
    
            (new Package([
                'name'=>'PROFESSIONAL',
                'vip'=>'vip4',
                'point_value'=>45,
                'registration_value'=>2220002,
                'value'=>157000,
                'profit_pool_eligible'=>false,
            ]))->save();
    
            (new Package([
                'name'=>'PREMIUM',
                'vip'=>'vip5',
                'point_value'=>70,
                'registration_value'=>342000,
                'value'=>245000,
                'profit_pool_eligible'=>true,
            ]))->save();
        }
    }
}
