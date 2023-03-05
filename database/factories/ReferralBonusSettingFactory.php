<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReferralBonusSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'package_id'=>$this->faker->randomElement([1,2,3]),
            'generation_1_percentage'=>25,
            'generation_2_percentage'=>5,
            'generation_3_percentage'=>1,
            'generation_4_percentage'=>1,
            'generation_5_percentage'=>1,
            'generation_6_percentage'=>1
        ];
    }
}
