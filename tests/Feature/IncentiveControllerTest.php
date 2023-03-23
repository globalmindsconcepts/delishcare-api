<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class IncentiveControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_incentive()
    {
        //$setting = $this->createSetting()->first();
        $admin = $this->setAdmin();
        $rank = $this->createRank()->first();

        Storage::fake('incentives');

        $data = ['rank_id' => $rank->id,'worth'=>2000,'incentive'=>'Smart phone',
        'image'=> UploadedFile::fake()->image('incentive.png')];

        $response = $this->actingAs($admin)->postJson($this->v1API("incentives/create"), $data);

        $response->assertOk();
        //Storage::disk('incentives')->assertExists('incentive.png');
        $this->assertDatabaseHas('incentives', ['rank_id' => $data['rank_id'],'file_path'=>'file.png']);
    }

    public function test_update_incentive_with_file()
    {
        $admin = $this->setAdmin();
        $rank = $this->createRank()->first();
        $incentive = $this->createIncentive(['rank_id' => $rank->id, 'worth' => 2000, 
        'incentive' => 'smart phone','file_path'=> 'incentive.png']);

        Storage::fake('incentives');

        $data = ['rank_id' => $rank->id,'worth'=>1000,'incentive'=>'Smart phone',
        'image'=> UploadedFile::fake()->image('incentive1.png')];

        $response = $this->actingAs($admin)->postJson($this->v1API("incentives/{$incentive->id}/update"), $data);

        $response->assertOk();
        $this->assertDatabaseHas('incentives', ['worth' => $data['worth'],'file_path'=>'file.png']);
    }

    public function test_update_incentive_without_file()
    {
        $admin = $this->setAdmin();
        $rank = $this->createRank()->first();
        $incentive = $this->createIncentive(['rank_id' => $rank->id, 'worth' => 2000, 'incentive' => 'smart phone']);

        $data = ['rank_id' => $rank->id,'worth'=>1000,'incentive'=>'Smart phone'];

        $response = $this->actingAs($admin)->postJson($this->v1API("incentives/{$incentive->id}/update"), $data);

        $response->assertOk();
        $this->assertDatabaseHas('incentives', ['worth' => $data['worth']]);
    }

    public function test_get_incentive()
    {
        $admin = $this->setAdmin();
        $rank = $this->createRank()->first();
        $incentive = $this->createIncentive(['rank_id' => $rank->id, 'worth' => 2000, 'incentive' => 'smart phone']);

        $response = $this->actingAs($admin)->getJson($this->v1API("incentives/{$incentive->id}"));
        $response->assertOk();

        $response->assertJsonStructure(['data', 'success','message','status']);
    }

    public function test_get_all_incentives()
    {
        $admin = $this->setAdmin();
        $rank = $this->createRank()->first();
        $incentive = $this->createIncentive(['rank_id' => $rank->id, 'worth' => 2000, 'incentive' => 'smart phone']);

        $response = $this->actingAs($admin)->getJson($this->v1API("incentives/all"));
        $response->assertOk();

        $response->assertJsonStructure(['data', 'success','status']);
    }

}
