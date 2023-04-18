<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductClaimControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_product_claim()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        (new \App\Models\PackagePayment([
            'user_uuid' => $user->uuid,
            'amount' => 20000,
            'point_value' => $package->point_value,
            'reference' => 'hdhdhyey34y5jeje'
        ]))->save();

        $product = $this->createProduct(['points'=>2])->first();
        $product2 = $this->createProduct(['points'=>1])->first();
        $data = [
            'product_ids' => [$product->id,$product2->id],
        ];

        $response = $this->actingAs($user)->postJson($this->v1API("product-claims/{$user->uuid}/create"), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_claims', ['user_uuid' => $user->uuid, 'product_id' => $data['product_ids'][0],'status'=>'processing']);
    }

    public function test_user_product_claims()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $product = $this->createProduct(['points'=>2])->first();
        $product2 = $this->createProduct(['points'=>1])->first();

        $this->createProductClaim(['product_id'=>$product->id,'user_uuid'=>$user->uuid]);
        $this->createProductClaim(['product_id'=>$product2->id,'user_uuid'=>$user->uuid]);

        $response = $this->actingAs($user)->getJson($this->v1API("product-claims/{$user->uuid}/claims"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_claims', ['user_uuid' => $user->uuid,'status'=>'processing']);
    }

    public function test_all_product_claims()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $product = $this->createProduct(['points'=>2])->first();
        $product2 = $this->createProduct(['points'=>1])->first();

        $this->createProductClaim(['product_id'=>$product->id,'user_uuid'=>$user->uuid]);
        $this->createProductClaim(['product_id'=>$product2->id,'user_uuid'=>$user->uuid]);

        $response = $this->actingAs($user)->getJson($this->v1API("product-claims/all"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_claims', ['user_uuid' => $user->uuid,'status'=>'processing']);
    }

    public function test_approve_product_claim()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $product = $this->createProduct(['points'=>2])->first();
        $product2 = $this->createProduct(['points'=>1])->first();

        $this->createProductClaim(['product_id'=>$product->id,'user_uuid'=>$user->uuid]);
        $this->createProductClaim(['product_id'=>$product2->id,'user_uuid'=>$user->uuid]);

        $response = $this->actingAs($user)->putJson($this->v1API("product-claims/{$user->uuid}/approve"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_claims', ['user_uuid' => $user->uuid,'status'=>'approved']);
    }

    public function test_reject_product_claim()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $product = $this->createProduct(['points'=>2])->first();
        $product2 = $this->createProduct(['points'=>1])->first();

        $this->createProductClaim(['product_id'=>$product->id,'user_uuid'=>$user->uuid]);
        $this->createProductClaim(['product_id'=>$product2->id,'user_uuid'=>$user->uuid]);

        $response = $this->actingAs($user)->putJson($this->v1API("product-claims/{$user->uuid}/decline"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_claims', ['user_uuid' => $user->uuid,'status'=>'declined']);
    }

    public function test_total_product_sold()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $product = $this->createProduct(['points'=>2])->first();
        $product2 = $this->createProduct(['points'=>1])->first();

        $this->createProductClaim(['product_id'=>$product->id,'user_uuid'=>$user->uuid,'status'=>'approved']);
        $this->createProductClaim(['product_id'=>$product2->id,'user_uuid'=>$user->uuid,'status'=>'approved']);

        $response = $this->actingAs($user)->getJson($this->v1API("product-claims/total-product-sold"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_claims', ['user_uuid' => $user->uuid,'status'=>'approved']);
    }

    public function test_total_product_pv_sold()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $product = $this->createProduct(['points'=>2])->first();
        $product2 = $this->createProduct(['points'=>1])->first();

        $this->createProductClaim(['product_id'=>$product->id,'user_uuid'=>$user->uuid,'status'=>'approved','points'=>2]);
        $this->createProductClaim(['product_id'=>$product2->id,'user_uuid'=>$user->uuid,'status'=>'approved','points'=>1]);

        $response = $this->actingAs($user)->getJson($this->v1API("product-claims/total-product-pv"));

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_claims', ['user_uuid' => $user->uuid,'status'=>'approved']);
        $response->assertJson(['data'=>3,'status'=>200]);
    }
}
