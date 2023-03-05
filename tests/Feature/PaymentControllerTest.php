<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_initiate_payment()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $data = [
            'amount'=>20,
            'description'=>'package payment',
            'txn_source'=>'package_payment'
        ];
        $response = $this->actingAs($user)->postJson($this->v1API('payments/initiate'),
        $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions',['amount'=>$data['amount'],'txn_source'=>$data['txn_source'],'txn_type'=>'credit']);
    }

    public function test_verify_payment()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 50, 
        'registration_value' => 20000])->first();

        $user1 = $this->createUsers(['package_id'=>$package->id])->first();

        $this->createReferralBonusSetting(['package_id' => $package->id]);
        $setting = $this->createSetting(['unit_point_value' => 3500,'welcome_bonus_percentage'=>10]);
        $user = $this->createUsers(1,['package_id'=>$package->id])->last();

        $this->createReferral(['referrer_uuid' => $user1->uuid, 'referred_uuid' => $user->uuid]);

        $data = [
            'amount'=>20,
            'description'=>'wallet funding',
            'txn_source'=>'wallet_account'
        ];

        $response = $this->actingAs($user)->postJson($this->v1API('payments/initiate'),$data);
        
        $trans = \App\Models\Transaction::first();
        
        $data = [
            'reference'=>$trans->txn_reference,
        ];

        $welcome_bonus = ($setting->welcome_bonus_percentage/100 * $package->point_value) * $setting->unit_point_value;

        $response = $this->actingAs($user)->postJson($this->v1API('payments/verify'),$data);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions',['txn_reference'=>$data['reference'],'txn_status'=>'successful']);
        $this->assertDatabaseHas('package_payments',['reference'=>$data['reference'],'status'=>'approved']);
        $this->assertDatabaseHas('welcome_bonuses',['user_uuid'=>$user->uuid, 'bonus'=> number_format($welcome_bonus,1,'.','')]);
    }
    
}
