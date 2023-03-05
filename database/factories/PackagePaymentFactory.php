<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PackagePaymentFactory extends Factory
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
            'amount'=>$this->faker->randomDigitNotZero(),
            'point_value'=>'',
            'status'=>'processing',
            'reference'=>'',
            'processor'=>'paystack',
            'updated_at'=>''
        ];
    }
}
