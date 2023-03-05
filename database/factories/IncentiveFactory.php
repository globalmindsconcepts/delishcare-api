<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IncentiveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'rank_id'=>'',
            'points'=>'',
            'worth'=>'',
            'incentive'=>''
        ];
    }
}
