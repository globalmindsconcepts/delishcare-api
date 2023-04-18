<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReferralBonusSetting;

class ReferralBonusSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!ReferralBonusSetting::first()){

            (new ReferralBonusSetting([
                'package_id'=>1,
                'generation_1_percentage'=>5,
                'generation_2_percentage'=>5,
                'generation_3_percentage'=>5,
                'generation_4_percentage'=>5,
                'generation_5_percentage'=>5,
                'generation_6_percentage'=>5,
            ]))->save();

            (new ReferralBonusSetting([
                'package_id'=>2,
                'generation_1_percentage'=>5,
                'generation_2_percentage'=>5,
                'generation_3_percentage'=>5,
                'generation_4_percentage'=>5,
                'generation_5_percentage'=>5,
                'generation_6_percentage'=>5,
            ]
                
            ))->save();

            (new ReferralBonusSetting([
                'package_id'=>3,
                'generation_1_percentage'=>5,
                'generation_2_percentage'=>5,
                'generation_3_percentage'=>5,
                'generation_4_percentage'=>5,
                'generation_5_percentage'=>5,
                'generation_6_percentage'=>5,
            ]
                
            ))->save();

            (new ReferralBonusSetting([
                'package_id'=>4,
                'generation_1_percentage'=>5,
                'generation_2_percentage'=>5,
                'generation_3_percentage'=>5,
                'generation_4_percentage'=>5,
                'generation_5_percentage'=>5,
                'generation_6_percentage'=>5,
            ]
                
            ))->Save();

            (new ReferralBonusSetting([
                'package_id'=>5,
                'generation_1_percentage'=>5,
                'generation_2_percentage'=>5,
                'generation_3_percentage'=>5,
                'generation_4_percentage'=>5,
                'generation_5_percentage'=>5,
                'generation_6_percentage'=>5,
            ]
                
            ))->save();

            (new ReferralBonusSetting([
                'package_id'=>6,
                'generation_1_percentage'=>5,
                'generation_2_percentage'=>5,
                'generation_3_percentage'=>5,
                'generation_4_percentage'=>5,
                'generation_5_percentage'=>5,
                'generation_6_percentage'=>5,
            ]
                
            ))->save();
        }
    }
}
