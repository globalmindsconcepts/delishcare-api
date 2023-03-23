<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PackageControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_package()
    {
        //$package = $this->createPackage();
        $admin = $this->setAdmin();

        $data = [
            'name'=>'basic',
            'vip'=>'vip1',
            'point_value'=>60,
            'value'=>3500 * 60,
            'registration_value'=>50000,
        ];

        $response = $this->actingAs($admin)->postJson($this->v1API("packages/create"),$data);

        $response->assertOk();

        $this->assertDatabaseHas('packages', ['name' => 'basic']);
        $response->assertJsonStructure(['data', 'message', 'status']);
    }

    private function test_update_package()
    {
        $package = $this->createPackage();
        $admin = $this->setAdmin();

        $data = [
            'name'=>'premium',
            'vip'=>'vip1',
            'point_value'=>60,
            'value'=>3500 * 60,
            'registration_value'=>50000,
        ];

        $response = $this->actingAs($admin)->putJson($this->v1API("packages/{$package->id}/update"),$data);

        $response->assertOk();
        $response->assertJsonStructure([ 'message', 'status']);

        $this->assertDatabaseHas('packages', ['name' => 'premium']);
    }

    public function test_get_package()
    {
        $package = $this->createPackage();
        $admin = $this->setAdmin();

        $response = $this->actingAs($admin)->getJson($this->v1API("packages/{$package->id}"));

        $response->assertOk();

        $response->assertJsonStructure(['data', 'success', 'status']);
        $this->assertDatabaseHas('packages', ['id' => $package->id]);
    }

    public function test_delete_package()
    {
        $package = $this->createPackage();
        $admin = $this->setAdmin();

        $response = $this->actingAs($admin)->deleteJson($this->v1API("packages/{$package->id}"));

        $response->assertOk();

        $response->assertJsonStructure(['message', 'status']);
        $this->assertDatabaseMissing('packages', ['id' => $package->id]);
    }
    public function test_get_all_packages()
    {
        $package = $this->createPackage();
        $admin = $this->setAdmin();

        $response = $this->actingAs($admin)->getJson($this->v1API("packages/all"));

        $response->assertOk();

        $response->assertJsonStructure(['data', 'success', 'status']);
        $this->assertDatabaseHas('packages', ['id' => $package->id]);
    }
}
