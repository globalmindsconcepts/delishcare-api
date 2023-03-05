<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WelcomeBonusFactory extends Factory
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
            'bonus'=> 2000
        ];
    }
}
