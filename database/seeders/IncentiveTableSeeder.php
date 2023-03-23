<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incentive;

class IncentiveTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Incentive::first()){
            (new Incentive([
                'rank_id'=>1,
                'worth'=>12600,
                'incentive'=>'smart phone'
            ]))->save();
    
            (new Incentive([
                'rank_id'=>2,
                'worth'=>25200,
            ]))->save();
    
            (new Incentive([
                'rank_id'=>3,
                'worth'=>75600,
            ]))->save();
    
            (new Incentive([
                'rank_id'=>4,
                'worth'=>3024000,
                'incentive'=>'Dubai trip'
            ]))->save();
    
            (new Incentive([
                'rank_id'=>5,
                'worth'=>6804000,
                'incentive'=>'car award'
            ]))->save();
    
            (new Incentive([
                'rank_id'=>6,
                'worth'=>20412000,
                'incentive'=>'SUV'
            ]))->save();
    
            (new Incentive([
                'rank_id'=>7,
                'worth'=>81448000,
                'incentive'=>'Detached duplex'
            ]))->save();
        }
        
    }
}
