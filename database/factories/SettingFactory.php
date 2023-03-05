<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'unit_point_value'=>$this->faker->randomDigitNotZero(),
            'welcome_bonus_percentage'=>2,
            'incentive_percentage'=>2,
            'equillibrum_bonus'=>$this->faker->randomDigitNotZero(),
            'loyalty_bonus_percentage'=>2,

            'profit_pool_percentage'=>2,
            'profit_pool_duration'=>6,
            'profit_pool_days_offset'=>30,
            'profit_pool_num_of_downlines'=>4,

            'minimum_withdrawal'=>1000,
            'maximum_withdrawal'=>5000,

            'global_profit_first_percentage'=>10,
            'global_profit_second_percentage'=>10,
            'next_global_profit_share_month'=>10,
            'next_global_profit_share_day'=>30,

            'placement_bonus_percentage'=>25
        ];
    }
}
