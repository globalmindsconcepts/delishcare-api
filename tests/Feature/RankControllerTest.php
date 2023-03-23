<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RankControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_rank()
    {
        $admin = $this->setAdmin();

        $data = [
            'name'=>'basic',
            'points'=>60,
        ];

        $response = $this->actingAs($admin)->postJson($this->v1API("ranks/create"),$data);

        $response->assertOk();

        $this->assertDatabaseHas('ranks', ['name' => 'basic']);
        $response->assertJsonStructure(['data', 'message', 'status']);
    }

    public function test_update_rank()
    {
        $admin = $this->setAdmin();
        $rank = $this->createRank()->first();

        $data = [
            'name'=>'diamond',
            'points'=>60,
        ];

        $response = $this->actingAs($admin)->putJson($this->v1API("ranks/{$rank->id}/update"),$data);

        $response->assertOk();

        $this->assertDatabaseHas('ranks', ['name' => 'diamond']);
        $response->assertJsonStructure(['success', 'message', 'status']);
    }

    public function test_get_rank()
    {
        $admin = $this->setAdmin();
        $rank = $this->createRank()->first();

        $response = $this->actingAs($admin)->getJson($this->v1API("ranks/{$rank->id}"));

        $response->assertOk();

        $this->assertDatabaseHas('ranks', ['name' => $rank->name]);
        $response->assertJsonStructure(['data', 'message', 'status']);
    }

    public function test_get_all_ranks()
    {
        $admin = $this->setAdmin();
        $rank = $this->createRank()->first();

        $response = $this->actingAs($admin)->getJson($this->v1API("ranks/all"));

        $response->assertOk();

        $this->assertDatabaseHas('ranks', ['name' => $rank->name]);
        $response->assertJsonStructure(['data', 'status']);
    }
}
