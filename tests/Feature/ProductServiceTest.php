<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_service()
    {
        $admin = $this->setAdmin();
        $provider = $this->createProvider()->first();
        $data = ['service'=>'payment','default_provider_id'=>$provider->id];

        $response = $this->actingAs($admin)->postJson($this->v1API('product-services/create'), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_services', ['service' => $data['service']]);
    }

    public function test_update_service()
    {
        $admin = $this->setAdmin();
        $provider = $this->createProvider()->first();
        $servie = $this->createService(['default_provider_id' => $provider->id]);
        $data = ['service'=>'paystack','default_provider_id'=>$provider->id];
        $id = $servie->id;
        $response = $this->actingAs($admin)->putJson($this->v1API("product-services/${id}/update"), $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('product_services', ['service' => $data['service'],'id'=>$servie->id]);
    }

    public function test_get_service()
    {
        $admin = $this->setAdmin();
        $provider = $this->createProvider()->first();
        $servie = $this->createService(['default_provider_id' => $provider->id]);
        //$data = ['service'=>'paystack','default_provider_id'=>$provider->id];
        $id = $servie->id;
        $response = $this->actingAs($admin)->getJson($this->v1API("product-services/${id}"));
        $response->assertStatus(200);
        $this->assertDatabaseHas('product_services', ['id'=>$servie->id]);
        $response->assertJsonStructure(['data', 'status']);
    }

    public function test_get_all_services()
    {
        $admin = $this->setAdmin();
        $provider = $this->createProvider()->first();
        $servie = $this->createService(['default_provider_id' => $provider->id]);
        //$data = ['service'=>'paystack','default_provider_id'=>$provider->id];
        $id = $servie->id;
        $response = $this->actingAs($admin)->getJson($this->v1API("product-services/all"));
        $response->assertStatus(200);
        $this->assertDatabaseHas('product_services', ['id'=>$servie->id]);
        $response->assertJsonStructure(['data', 'status']);
    }

    public function test_delete_service()
    {
        $admin = $this->setAdmin();
        $provider = $this->createProvider()->first();
        $servie = $this->createService(['default_provider_id' => $provider->id]);
        //$data = ['service'=>'paystack','default_provider_id'=>$provider->id];
        $id = $servie->id;
        $response = $this->actingAs($admin)->deleteJson($this->v1API("product-services/{$id}"));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('product_services', ['id'=>$servie->id]);
        $response->assertJsonStructure(['message', 'status']);
    }
}
