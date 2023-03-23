<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_service_provider()
    {
        $admin = $this->setAdmin();

        $data = ['name' => 'paystack']; //$this->createProvider();
        $response = $this->actingAs($admin)->postJson($this->v1API('service-providers/create'),$data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('service_providers', ['name' => $data['name']]);
    }

    public function test_update_service_provider()
    {
        $admin = $this->setAdmin();
        $provider = $this->createProvider()->first();
        $id = $provider->id;
        $data = ['name' => 'paystac']; //$this->createProvider();
        $response = $this->actingAs($admin)->putJson($this->v1API("service-providers/${id}/update"),$data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('service_providers', ['name' => $data['name']]);
    }

    public function test_get_all_service_providers()
    {
        $admin = $this->setAdmin();
        $provider = $this->createProvider()->first();
        $id = $provider->id;
        $data = ['name' => 'paystack']; //$this->createProvider();
        $response = $this->actingAs($admin)->getJson($this->v1API("service-providers/all"));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'status']);
    }

    public function test_get_service_provider()
    {
        $admin = $this->setAdmin();
        $provider = $this->createProvider()->first();
        $id = $provider->id;
        $data = ['name' => 'paystack']; //$this->createProvider();
        $response = $this->actingAs($admin)->getJson($this->v1API("service-providers/{$id}"));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'status', 'success']);
    }

    public function test_delete_service_provider()
    {
        $admin = $this->setAdmin();
        $provider = $this->createProvider()->first();
        $id = $provider->id;
        $data = ['name' => 'paystack']; //$this->createProvider();
        $response = $this->actingAs($admin)->deleteJson($this->v1API("service-providers/{$id}"));

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'status']);

        $this->assertDatabaseMissing('service_providers',['id' => $provider->id]);
    }

}
