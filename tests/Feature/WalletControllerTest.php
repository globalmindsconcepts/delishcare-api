<?php

namespace Tests\Feature;

use App\Models\PackagePayment;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class WalletControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_compute_profit_pool()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user1->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user2->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user3->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user4->uuid]);

        $this->createPackagePayment(['user_uuid' => $user->uuid, 'amount' => 20000, 
        'point_value' => $setting->point_value, 'reference' => 'hdhdhyey34y5jeje','updated_at'=>'2022-12-01']);

        (new PackagePayment([
            'user_uuid' => $user1->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user2->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user3->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user4->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje'
        ]))->save();

        // $this->createPackagePayment(['user_uuid' => $user1->uuid, 'amount' => 20000, 
        // 'point_value' => $setting->point_value, 'reference' => 'hdhdhyey34y5jeje','updated_at'=>'2022-12-01']);
        // $this->createPackagePayment(['user_uuid' => $user2->uuid, 'amount' => 20000, 
        // 'point_value' => $setting->point_value, 'reference' => 'hdhdhyey34y5jeje','updated_at'=>'2022-12-01']);
        // $this->createPackagePayment(['user_uuid' => $user3->uuid, 'amount' => 20000, 
        // 'point_value' => $setting->point_value, 'reference' => 'hdhdhyey34y5jeje','updated_at'=>'2022-12-01']);
        // $this->createPackagePayment(['user_uuid' => $user4->uuid, 'amount' => 20000, 
        // 'point_value' => $setting->point_value, 'reference' => 'hdhdhyey34y5jeje','updated_at'=>'2022-12-01']);

        try {
            (new WalletService)->computeProfitPool($user->uuid);
        } catch (\Exception $e) {
            Log::error("profit-pool-test", [$e]);
        }

        $this->assertDatabaseHas('profit_pools', ['user_uuid' => $user->uuid]);
    }

    public function test_fetch_profit_pool()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'value' => 2000
        ];

        $this->createProfitPool($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/{$user->uuid}/profit-pool"));
        $response->assertOk();

        $response->assertJson(['profit_pool' => $data['value'], 'success' => true]);

        $this->assertDatabaseHas('profit_pools', ['user_uuid' => $user->uuid, 'value' => $data['value']]);
    }

    public function test_fetch_user_profit_pools()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'value' => 2000
        ];

        $this->createProfitPool($data);
        $this->createProfitPool($data);
        $this->createProfitPool($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/{$user->uuid}/profit-pool"));
        $response->assertOk();

        $this->assertDatabaseCount('profit_pools', 3);
    }

    public function test_fetch_welcome_bonus()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'bonus' => 2000
        ];

        $this->createWelcomeBonus($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/{$user->uuid}/welcome-bonus"));

        $response->assertOk();
        $response->assertJson(['bonus' => $data['bonus'], 'success' => true]);
        $this->assertDatabaseHas('welcome_bonuses', ['user_uuid' => $user->uuid, 'bonus' => $data['bonus']]);
    }

    public function test_compute_equilibrum_bonus()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user1->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user2->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user3->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user4->uuid]);

        $bonus = (4 / 2) * $setting->equillibrum_bonus;
        info('bonus', [$bonus]);
        try {
            (new WalletService)->equillibrumBonus($user->uuid);
        } catch (\Exception $e) {
            Log::error("equilibrum-bonus-test", [$e]);
        }

        $this->assertDatabaseHas('equilibrum_bonuses', ['user_uuid' => $user->uuid,'value'=>$bonus]);
    }

    public function test_fetch_equilibrum_bonus()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'value' => 2000,
            'num_downlines'=>4,
            'bonus_value'=>5
        ];

        $this->createEquilibrumBonus($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/{$user->uuid}/equilibrum-bonus"));

        $response->assertOk();
        $response->assertJson(['bonus' => $data['value'], 'success' => true]);
        $this->assertDatabaseHas('equilibrum_bonuses', ['user_uuid' => $user->uuid, 'value' => $data['value']]);
    }

    public function test_compute_loyalty_bonus()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user1->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user2->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user3->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user4->uuid]);

        try {
            (new WalletService)->loyaltyBonus($user1->uuid);
        } catch (\Exception $e) {
            Log::error("loyalty-bonus-test", [$e]);
        }

        $equilibrum_bonus = \App\Models\EquilibrumBonus::first()->value;
        $bonus = $equilibrum_bonus * ($setting->loyalty_bonus_percentage/100);

        $this->assertDatabaseHas('equilibrum_bonuses', ['user_uuid' => $user->uuid, 'value' => $equilibrum_bonus]);
        $this->assertDatabaseHas('loyalty_bonuses', ['user_uuid' => $user1->uuid,'value'=>$bonus]);
    }

    public function test_fetch_loyalty_bonus()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'value' => 2000,
            'bonus_value'=>5
        ];

        $this->createLoyaltyBonus($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/{$user->uuid}/loyalty-bonus"));

        $response->assertOk();
        $response->assertJson(['bonus' => $data['value'], 'success' => true]);
        $this->assertDatabaseHas('loyalty_bonuses', ['user_uuid' => $user->uuid, 'value' => $data['value']]);
    }

    public function test_compute_global_profit()
    {
        $rank = $this->createRank(['name' => 'Mesater', 'points' => '100', 'is_global_profit_eligible' => true])->first();
        $setting = $this->createSetting(['next_global_profit_share_month'=>3,'next_global_profit_share_day'=>5])->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 20, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id,'rank_id'=>$rank->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        $this->createPackagePayment([
            'user_uuid' => $user->uuid,
            'amount' => $package->registration_value,
            'point_value' => $package->point_value,
            'reference' => '123jdjhdh4e',
            'updated_at' => '2023-03-05',
            'status'=>'approved',
        ]);
        $this->createPackagePayment([
            'user_uuid' => $user1->uuid,
            'amount' => $package->registration_value,
            'point_value' => $package->point_value,
            'reference' => '123jdjhdh4e',
            'updated_at' => '2023-03-05',
            'status'=>'approved',
        ]);
        $this->createPackagePayment([
            'user_uuid' => $user2->uuid,
            'amount' => $package->registration_value,
            'point_value' => $package->point_value,
            'reference' => '123jdjhdh4e',
            'updated_at' => '2023-03-05',
            'status'=>'approved',
        ]);
        $this->createPackagePayment([
            'user_uuid' => $user3->uuid,
            'amount' => $package->registration_value,
            'point_value' => $package->point_value,
            'reference' => '123jdjhdh4e',
            'updated_at' => '2023-03-05',
            'status'=>'approved',
        ]);
        $this->createPackagePayment([
            'user_uuid' => $user4->uuid,
            'amount' => $package->registration_value,
            'point_value' => $package->point_value,
            'reference' => '123jdjhdh4e',
            'updated_at' => '2023-03-05',
            'status'=>'approved',
        ]);

        $all_payments = \App\Models\PackagePayment::all();//->sum('point_value');
        $total_pvs = $all_payments->sum('point_value');
        $total_users = $all_payments->count();

        try {
            (new WalletService)->computeUserGlobalProfitShare($user->uuid,$total_users);
        } catch (\Exception $e) {
            Log::error("global-profit-bonus-test", [$e]);
        }

        $firstBonus = ($setting->global_profit_first_percentage / 100) * $total_pvs;
        $secondBonus = ($setting->global_profit_second_percentage / 100) * $firstBonus;
        $bonus = $secondBonus * $setting->unit_point_value;

        $this->assertDatabaseHas('global_profits', ['user_uuid' => $user->uuid, 'profit' => $bonus/$total_users]);
    }

    public function test_fetch_global_profit()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'profit' => 2000,
        ];

        $this->createGlobalProfit($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/{$user->uuid}/global-profit"));

        $response->assertOk();
        $response->assertJson(['bonus' => $data['profit'], 'success' => true]);
        $this->assertDatabaseHas('global_profits', ['user_uuid' => $user->uuid, 'profit' => $data['profit']]);
    }

    public function test_fetch_referral_bonus()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'generation' => 'generation_1',
            'bonus'=>2000
        ];

        $this->createReferralBonus($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/{$user->uuid}/referral-bonus"));

        $response->assertOk();
        $response->assertJson(['bonus' => $data['bonus'], 'success' => true]);
        $this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user->uuid, 'bonus' => $data['bonus']]);
    }

    public function test_fetch_placement_bonus()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'bonus'=>2000
        ];

        $this->createPlacementBonus($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/{$user->uuid}/placement-bonus"));

        $response->assertOk();
        $response->assertJson(['bonus' => $data['bonus'], 'success' => true]);
        $this->assertDatabaseHas('placement_bonuses', ['user_uuid' => $user->uuid, 'bonus' => $data['bonus']]);
    }

    public function test_get_total_bonus()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $profitPooldata = [
            'user_uuid' => $user->uuid,
            'value' => 2000
        ];
        $welcomeBonusData = [
            'user_uuid' => $user->uuid,
            'bonus' => 2000
        ];
        $equilibrumBonusData = [
            'user_uuid' => $user->uuid,
            'value' => 2000,
            'num_downlines'=>4,
            'bonus_value'=>5
        ];
        $loyaltyBonusData = [
            'user_uuid' => $user->uuid,
            'value' => 2000,
            'bonus_value'=>5
        ];
        $globalProfitData = [
            'user_uuid' => $user->uuid,
            'profit' => 2000,
        ];
        $referralBonusData = [
            'user_uuid' => $user->uuid,
            'generation' => 'generation_1',
            'bonus'=>2000
        ];
        $placementBonusData = [
            'user_uuid' => $user->uuid,
            'bonus'=>2000
        ];

        $this->createPlacementBonus($placementBonusData);
        $this->createReferralBonus($referralBonusData);
        $this->createGlobalProfit($globalProfitData);
        $this->createLoyaltyBonus($loyaltyBonusData);
        $this->createEquilibrumBonus($equilibrumBonusData);
        $this->createWelcomeBonus($welcomeBonusData);
        $this->createProfitPool($profitPooldata);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/{$user->uuid}/total-bonus"));

        $response->assertOk();
    }

    public function test_total_profit_pool_bonus()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'value' => 2000
        ];

        $this->createProfitPool($data);
        $this->createProfitPool($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/total-profit-pool-bonus"));
        $response->assertOk();

        $this->assertDatabaseHas('profit_pools', ['user_uuid' => $user->uuid, 'value' => $data['value']]);
        $response->assertJson(['data'=>4000]);
    }

    public function test_total_equilibrum_bonus()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'value' => 2000,
            'num_downlines'=>4,
            'bonus_value'=>5
        ];

        $this->createEquilibrumBonus($data);
        $this->createEquilibrumBonus($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/total-equilibrum-bonus"));

        $response->assertOk();
        $response->assertJson(['data' => 4000]);
        $this->assertDatabaseHas('equilibrum_bonuses', ['user_uuid' => $user->uuid, 'value' => $data['value']]);
    }

    public function test_total_loyalty_bonus()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'value' => 2000,
            'bonus_value'=>5
        ];

        $this->createLoyaltyBonus($data);
        $this->createLoyaltyBonus($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/total-loyalty-bonus"));

        $response->assertOk();
        $response->assertJson(['data' => 4000]);
        $this->assertDatabaseHas('loyalty_bonuses', ['user_uuid' => $user->uuid, 'value' => $data['value']]);
    }

    public function test_total_global_profit_bonus()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'user_uuid' => $user->uuid,
            'profit' => 2000,
        ];

        $this->createGlobalProfit($data);
        $this->createGlobalProfit($data);

        $response = $this->actingAs($user)->getJson($this->v1API("bonuses/total-global-profit-bonus"));

        $response->assertOk();
        $response->assertJson(['data' => 4000]);
        $this->assertDatabaseHas('global_profits', ['user_uuid' => $user->uuid, 'profit' => $data['profit']]);
    }

    public function test_company_wallet_balance()
    {
        $admin = $this->setAdmin();
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user1->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user2->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user3->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user4->uuid]);

        $this->createPackagePayment(['user_uuid' => $user->uuid, 'amount' => 20000, 
        'point_value' => $setting->point_value, 'reference' => 'hdhdhyey34y5jeje','updated_at'=>'2022-12-01']);

        (new PackagePayment([
            'user_uuid' => $user1->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user2->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user3->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user4->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();

        $this->createWithdrawal(['user_uuid'=>$user->uuid,'amount'=>10000,'status'=>'successful']);
        $this->createWithdrawal(['user_uuid'=>$user->uuid,'amount'=>10000,'status'=>'successful']);

        $response = $this->actingAs($admin)->getJson($this->v1API("bonuses/company-wallet-balance"));
        $response->assertOk();
        $response->assertJson(['data'=>60000]);
    }

    public function test_total_company_wallet()
    {
        $admin = $this->setAdmin();
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user1->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user2->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user3->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user4->uuid]);

        $this->createPackagePayment(['user_uuid' => $user->uuid, 'amount' => 20000, 
        'point_value' => $setting->point_value, 'reference' => 'hdhdhyey34y5jeje','updated_at'=>'2022-12-01']);

        (new PackagePayment([
            'user_uuid' => $user1->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user2->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user3->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user4->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();

        $this->createWithdrawal(['user_uuid'=>$user->uuid,'amount'=>10000,'status'=>'successful']);
        $this->createWithdrawal(['user_uuid'=>$user->uuid,'amount'=>10000,'status'=>'successful']);

        $response = $this->actingAs($admin)->getJson($this->v1API("bonuses/total-company-wallet"));
        $response->assertOk();
        $response->assertJson(['data'=>80000]);
    }
}


