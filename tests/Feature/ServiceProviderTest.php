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
    }

    public function test_update_service_provider()
    {
        $admin = $this->setAdmin();
        $provider = $this->createProvider()->first();
        $id = $provider->id;
        $data = ['name' => 'paystack']; //$this->createProvider();
        $response = $this->actingAs($admin)->putJson($this->v1API("service-providers/${id}/update"),$data);

        $response->assertStatus(200);
    }
}
