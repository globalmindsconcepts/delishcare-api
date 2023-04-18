<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function v1API($prefix){
        return '/api/'.$prefix;
    }

    public function createUsers($num=1,$data=['email'=>'larry@mail.com','password'=>'password']){
        return \App\Models\User::factory($num)->create($data);
    }
  
    public function createAdmin($num=1,$data=[]){
        return \App\Models\Admin::factory($num)->create($data);
    }
  
    public function setAdmin()
    {
        $user = $this->createAdmin(['email'=>'admin@mail.com'])->first();

        $response = $this->postJson($this->v1API('auth/admin-login'),
        ['email'=>'admin@mail.com','password'=>'password']);

        $response->assertStatus(200);
        return $user;
    }

    public function createPackage($data=null)
    {
        return \App\Models\Package::factory()->create($data);
    }

    public function createProvider()
    {
        return \App\Models\ServiceProvider::factory()->create();
    }

    public function createService($data=[])
    {
        return \App\Models\ProductService::factory()->create($data);
    }

    public function createReferral($data=[])
    {
        return \App\Models\Referral::factory()->create($data);
    }

    public function createReferralBonusSetting($data=null)
    {
        return \App\Models\ReferralBonusSetting::factory()->create($data);
    }

    public function createSetting($data=null)
    {
        return \App\Models\Setting::factory()->create($data);
    }

    public function createChild($data)
    {
        return \App\Models\Child::factory()->create($data);
    }

    public function createPackagePayment($data)
    {
        return \App\Models\PackagePayment::factory()->create($data);
    }

    public function createProfitPool($data=null)
    {
        return \App\Models\ProfitPool::factory()->create($data);
    }

    public function createWelcomeBonus($data=null)
    {
        return \App\Models\WelcomeBonus::factory()->create($data);
    }

    public function createEquilibrumBonus($data=null)
    {
        return \App\Models\EquilibrumBonus::factory()->create($data);
    }
    public function createLoyaltyBonus($data=null)
    {
        return \App\Models\LoyaltyBonus::factory()->create($data);
    }

    public function createRank($data=null)
    {
        return \App\Models\Rank::factory()->create($data);
    }

    public function createGlobalProfit($data=null)
    {
        return \App\Models\GlobalProfit::factory()->create($data);
    }

    public function createReferralBonus($data=null)
    {
        return \App\Models\ReferralBonus::factory()->create($data);
    }

    public function createPlacementBonus($data=null)
    {
        return \App\Models\PlacementBonus::factory()->create($data);
    }
    
    public function createIncentive($data=null)
    {
        return \App\Models\Incentive::factory()->create($data);
    }

    // public function createProduct($data=null)
    // {
    //     return \App\Models\Product::factory()->create($data);
    // }

    public function createIncentiveClaim($data=null)
    {
        return \App\Models\IncentiveClaim::factory()->create($data);
    }

    public function createProductClaim($data=null)
    {
        return \App\Models\ProductClaim::factory()->create($data);
    }

    public function createUserProfile($data=null)
    {
        return \App\Models\UserProfile::factory()->create($data);
    }

    public function createProduct($data=null,$create=true)
    {
        return $create==true ? \App\Models\Product::factory()->create($data) : \App\Models\Product::factory()->make($data)->toArray();
    }

    public function createWithdrawal($data=null,$create=true)
    {
        return $create==true ? \App\Models\Withdrawal::factory()->create($data) : \App\Models\Withdrawal::factory()->make($data)->toArray();
    }
}
