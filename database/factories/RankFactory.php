<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->randomElement(['master','premium']),
            'points'=>$this->faker->randomNumber(),
            'is_global_profit_eligible'=>false,
            'description'=>''
        ];
    }
}
