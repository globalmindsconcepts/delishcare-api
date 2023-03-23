<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WithdrawalFactory extends Factory
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
            'amount'=>$this->faker->randomDigit(),
            'processor'=>'fincra',
            'reference'=>Str::random(16)
        ];
    }
}
