<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReferralFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'referrer_uuid'=>'',
            'referred_uuid'=>'',
            'placer_uuid'=>''
        ];
    }
}
