<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IncentiveClaimControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_claim_incentive()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $rank = $this->createRank()->first();
        $incentive = $this->createIncentive(['rank_id' => $rank->id, 'worth' => 2000, 
        'incentive' => 'smart phone','file_path'=> 'incentive.png']);
        $data = [
            'user_uuid' => $user->uuid,
            'incentive_id' => $incentive->id,
        ];

        $response = $this->actingAs($user)->postJson($this->v1API('incentive-claims/create'), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('incentive_claims', ['user_uuid' => $data['user_uuid'], 'incentive_id' => $data['incentive_id'],'status'=>'processing']);
    }

    public function test_user_approve_incentive_claim()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $rank = $this->createRank()->first();
        $incentive = $this->createIncentive(['rank_id' => $rank->id, 'worth' => 2000, 
        'incentive' => 'smart phone','file_path'=> 'incentive.png']);
        $claim = $this->createIncentiveClaim([
            'user_uuid' => $user->uuid,
            'incentive_id' => $incentive->id,
        ]);

        $response = $this->actingAs($user)->putJson($this->v1API("incentive-claims/{$claim->id}/approve"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('incentive_claims', ['user_uuid' => $claim->user_uuid, 'incentive_id' => $claim->incentive_id,'status'=>'approved']);
    }

    public function test_user_decline_incentive_claim()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $rank = $this->createRank()->first();
        $incentive = $this->createIncentive(['rank_id' => $rank->id, 'worth' => 2000, 
        'incentive' => 'smart phone','file_path'=> 'incentive.png'])->first();
        $claim = $this->createIncentiveClaim([
            'user_uuid' => $user->uuid,
            'incentive_id' => $incentive->id,
        ])->first();

        $response = $this->actingAs($user)->putJson($this->v1API("incentive-claims/{$claim->id}/decline"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('incentive_claims', ['user_uuid' => $claim->user_uuid, 'incentive_id' => $claim->incentive_id,'status'=>'declined']);
    }

    public function test_all_incentive_claims()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $rank = $this->createRank()->first();
        $incentive = $this->createIncentive(['rank_id' => $rank->id, 'worth' => 2000, 
        'incentive' => 'smart phone','file_path'=> 'incentive.png'])->first();
        $claim = $this->createIncentiveClaim([
            'user_uuid' => $user->uuid,
            'incentive_id' => $incentive->id,
        ])->first();

        $response = $this->actingAs($user)->getJson($this->v1API("incentive-claims/all"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('incentive_claims', ['user_uuid' => $claim->user_uuid, 'incentive_id' => $claim->incentive_id,'status'=>'processing']);
        $response->assertJsonStructure(['data', 'status', 'success']);
    }

    public function test_user_claimed_incentives()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $rank = $this->createRank()->first();
        $incentive = $this->createIncentive(['rank_id' => $rank->id, 'worth' => 2000, 
        'incentive' => 'smart phone','file_path'=> 'incentive.png'])->first();
        $claim = $this->createIncentiveClaim([
            'user_uuid' => $user->uuid,
            'incentive_id' => $incentive->id,
        ])->first();

        $response = $this->actingAs($user)->getJson($this->v1API("incentive-claims/{$user->uuid}/claims"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('incentive_claims', ['user_uuid' => $claim->user_uuid, 'incentive_id' => $claim->incentive_id,'status'=>'processing']);
        $response->assertJsonStructure(['data', 'status']);
    }
}
