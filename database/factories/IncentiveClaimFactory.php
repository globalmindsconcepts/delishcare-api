<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IncentiveClaimFactory extends Factory
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
            'incentive_id'=>''
        ];
    }
}
