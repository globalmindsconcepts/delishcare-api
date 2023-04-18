<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Setting::first()){
            (new Setting([
                'unit_point_value'=>3500,
                'welcome_bonus_percentage'=>2,
                'equillibrum_bonus'=>1000,
                'loyalty_bonus_percentage'=>2,
                'profit_pool_percentage'=>2,
                'profit_pool_duration'=>6,
                'profit_pool_days_offset'=>30,
                'profit_pool_num_of_downlines'=>4,
                //'profit_pool_packages'=>
                'minimum_withdrawal'=>2000,
                'maximum_withdrawal'=>2000,
                'global_profit_first_percentage'=>5,
                'global_profit_second_percentage'=>10,
                'next_global_profit_share_month'=>10,
                'next_global_profit_share_day'=>30,
                'placement_bonus_percentage'=>5,
                
                'general_message'=>'Welcome to Delish care',
                'home_page_message'=>'Welcome to Delish care'
            ]))->save();
        }
        
    }
}
