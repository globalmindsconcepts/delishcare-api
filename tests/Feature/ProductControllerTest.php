<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_product()
    {
        $admin = $this->setAdmin();

        $product = $this->createProduct(null,false);

        $response = $this->actingAs($admin)->postJson($this->v1API('products/create'),$product);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products',['name'=>$product['name'],'points'=>$product['points'],'worth'=>$product['worth']]);
    }

    public function test_update_product()
    {
        $admin = $this->setAdmin();

        $product = $this->createProduct(null,true)->first();
        $data = $this->createProduct(null,false);

        $response = $this->actingAs($admin)->putJson($this->v1API("products/{$product->id}/update"),$data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products',['name'=>$data['name'],'points'=>$data['points'],'worth'=>$data['worth']]);
    }

    public function test_get_product()
    {
        $admin = $this->setAdmin();

        $product = $this->createProduct(null,true)->first();
        

        $response = $this->actingAs($admin)->getJson($this->v1API("products/{$product->id}"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('products',['name'=>$product->name,'points'=>$product->points,'worth'=>$product->worth]);
    }

    public function test_get_all_products()
    {
        $admin = $this->setAdmin();

        $product = $this->createProduct(null,true)->first();
        

        $response = $this->actingAs($admin)->getJson($this->v1API("products/all"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('products',['name'=>$product->name,'points'=>$product->points,'worth'=>$product->worth]);
    }
}
