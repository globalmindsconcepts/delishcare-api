<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->randomElement(['basic','premium']),
            'vip'=>$this->faker->randomElement(['vip1','vip6']),
            'point_value'=>60,
            'value'=>3500 * 60,
            'registration_value'=>50000,
            'profit_pool_eligible'=>false,
            'description'=>''
        ];
    }
}
