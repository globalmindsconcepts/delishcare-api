<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PackagePayment;
use App\Models\WelcomeBonus;

class SingleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!User::first()){
            (new User([
                'first_name'=>'Delishcare',
                'last_name'=>'Delsihcare',
                'email'=>'delishcare@mail.com',
                'username'=>'delishcare',
                'package_id'=>1
            ]))->save();

            (new Transaction([
                'user_uuid' => User::first()->uuid,
                'amount' => 20000,
                'txn_reference' => 'deliscare001',
                'txn_status'=>'successful',
                'narration'=>'payment',
                'txn_type'=>'credit',
                'txn_source'=>'package_payment'
            ]))->save();

            (new PackagePayment([
                'user_uuid'=>User::first()->uuid,
                'amount'=>20000,
                'point_value'=>5,
                'status'=>'approved',
                'reference'=>'deliscare001',
                'processor'=>'paystack'
            ]))->save();

            (new WelcomeBonus([
                'user_uuid'=>User::first()->uuid,
                'bonus'=>20000,
            ]))->save();
        }
    }
}
