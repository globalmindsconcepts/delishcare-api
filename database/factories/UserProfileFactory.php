<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
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
            'gender'=>'male',
            'bank_name'=>'first bank',
            'bank_account_name'=>'Joshua',
            'bank_account_number'=>000000000,
            'address'=>'',
            //''
        ];
    }
}
