<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EquilibrumBonusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_uuid'=>'',
            'value'=>'',
            'num_downlines'=>'',
            'bonus_value'=>''
        ];
    }
}
