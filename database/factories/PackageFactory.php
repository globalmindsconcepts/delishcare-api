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
            'name'=>'',
            'vip'=>'',
            'point_value'=>'',
            'value'=>2,
            'registration_value'=>1000,
            'profit_pool_eligible'=>false,
            'description'=>''
        ];
    }
}
