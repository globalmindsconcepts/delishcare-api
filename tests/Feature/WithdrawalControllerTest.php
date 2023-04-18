<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;


class WithdrawalControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_initiate_withdrawal()
    {
        $user = $this->createUsers()->first();
        $data = ['amount'=>200,'reference'=>Str::random(10)];
        $response = $this->actingAs($user)->post($this->v1API("withdrawals/{$user->uuid}/initiate"),$data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('withdrawals',['user_uuid'=>$user->uuid,'amount'=>$data['amount']]);
    }

    public function test_all_withdrawals()
    {
        $user = $this->createUsers()->first();
        $this->createWithdrawal(['user_uuid'=>$user->uuid]);
        $this->createWithdrawal(['user_uuid'=>$user->uuid]);
       
        $response = $this->actingAs($user)->get($this->v1API("withdrawals/all"));

        $response->assertStatus(200);
        $this->assertDatabaseCount('withdrawals',2);
    }

    public function test_fetch_withdrawal_details()
    {
        $user = $this->createUsers()->first();
        $with = $this->createWithdrawal(['user_uuid'=>$user->uuid])->first();
        
       
        $response = $this->actingAs($user)->get($this->v1API("withdrawals/{$with->id}/details"));

        $response->assertStatus(200);
        $this->assertDatabaseCount('withdrawals',1);
    }

    public function test_user_withdrawals()
    {
        $user = $this->createUsers()->first();
        $this->createWithdrawal(['user_uuid'=>$user->uuid]);
        $this->createWithdrawal(['user_uuid'=>$user->uuid]);
       
        $response = $this->actingAs($user)->get($this->v1API("withdrawals/{$user->uuid}/user-history"));

        $response->assertStatus(200);
        $this->assertDatabaseCount('withdrawals',2);
    }

    public function test_user_total_withdrawals()
    {
        $user = $this->createUsers()->first();
        $with1 = $this->createWithdrawal(['user_uuid'=>$user->uuid,'amount'=>1000])->first();
        $with2 = $this->createWithdrawal(['user_uuid'=>$user->uuid,'amount'=>2000])->get()->last();

        $total = $with1->amount + $with2->amount;
       
        $response = $this->actingAs($user)->get($this->v1API("withdrawals/{$user->uuid}/user-total"));

        $response->assertStatus(200);
        $this->assertDatabaseCount('withdrawals',2);
        $this->assertEquals($total,3000);
    }

    public function test_total_withdrawals()
    {
        $user = $this->createUsers()->first();
        $with1 = $this->createWithdrawal(['user_uuid'=>$user->uuid,'amount'=>1000])->first();
        $with2 = $this->createWithdrawal(['user_uuid'=>$user->uuid,'amount'=>2000])->get()->last();

        $total = $with1->amount + $with2->amount;
       
        $response = $this->actingAs($user)->get($this->v1API("withdrawals/{$user->uuid}/user-total"));

        $response->assertStatus(200);
        $this->assertDatabaseCount('withdrawals',2);
        $this->assertEquals($total,3000);
    }
}
