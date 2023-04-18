<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\PackagePayment;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_downlines()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        (new PackagePayment([
            'user_uuid' => $user->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje'
        ]))->save();

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

        $this->createReferral([
            'referrer_uuid'=>$user->uuid,
            'referred_uuid'=>$user1->uuid
        ]);

        $this->createReferral([
            'referrer_uuid'=>$user->uuid,
            'referred_uuid'=>$user2->uuid
        ]);

        $this->createReferral([
            'referrer_uuid'=>$user2->uuid,
            'referred_uuid'=>$user3->uuid
        ]);

        $this->createReferral([
            'referrer_uuid'=>$user2->uuid,
            'referred_uuid'=>$user4->uuid
        ]);

        $response = $this->actingAs($user)->getJson($this->v1API("users/{$user->uuid}/downlines"));
        $response->assertOk();
        $this->assertDatabaseHas('referrals',['referrer_uuid'=>$user2->uuid]);
        $this->assertDatabaseCount('referrals',4);
    }

    public function test_direct_downlines()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        (new PackagePayment([
            'user_uuid' => $user->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje'
        ]))->save();

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

        $this->createReferral([
            'referrer_uuid'=>$user->uuid,
            'referred_uuid'=>$user1->uuid
        ]);

        $this->createReferral([
            'referrer_uuid'=>$user->uuid,
            'referred_uuid'=>$user2->uuid
        ]);

        $this->createReferral([
            'referrer_uuid'=>$user2->uuid,
            'referred_uuid'=>$user3->uuid
        ]);

        $this->createReferral([
            'referrer_uuid'=>$user2->uuid,
            'referred_uuid'=>$user4->uuid
        ]);

        $response = $this->actingAs($user)->getJson($this->v1API("users/{$user->uuid}/direct-downlines"));
        $response->assertOk();
        $this->assertDatabaseHas('referrals',['referrer_uuid'=>$user2->uuid]);
        $this->assertDatabaseCount('referrals',4);
    }

    public function test_upline_details()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id])->first();
        (new PackagePayment([
            'user_uuid' => $user->uuid,
            'amount' => 20000,
            'point_value' => $setting->point_value,
            'reference' => 'hdhdhyey34y5jeje'
        ]))->save();

        $this->createReferral([
            'referrer_uuid'=>$user->uuid,
            'referred_uuid'=>$user1->uuid
        ]);

        $response = $this->actingAs($user1)->getJson($this->v1API("users/{$user1->uuid}/upline-details"));
        $response->assertOk();
        $this->assertDatabaseHas('referrals',['referrer_uuid'=>$user->uuid]);
        //$this->assertDatabaseCount('referrals',4);
    }

    public function test_update_user()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
    
        $data = [
            'first_name'=>'Tunde',
            'last_name'=>'Josh',
            'phone'=>'09034'
        ];

        $response = $this->actingAs($user)->putJson($this->v1API("users/{$user->uuid}/update"),$data);
        $response->assertOk();
    }

    public function test_total_point_values()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user1->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user2->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user3->uuid]);

        $response = $this->actingAs($user)->getJson($this->v1API("users/{$user->uuid}/total-pv"));
        $response->assertOk();
    }

    public function test_genealogy()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        $this->createUserProfile(['user_uuid'=>$user->uuid]);
        $this->createUserProfile(['user_uuid'=>$user1->uuid]);
        $this->createUserProfile(['user_uuid'=>$user2->uuid]);
        $this->createUserProfile(['user_uuid'=>$user3->uuid]);
        $this->createUserProfile(['user_uuid'=>$user4->uuid]);

        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user1->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user2->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user3->uuid]);
        $this->createChild(['parent_id' => $user->uuid, 'child_id' => $user4->uuid]);

        $response = $this->actingAs($user)->getJson($this->v1API(("users/{$user->uuid}/genealogy")));
        $response->assertOk();
    }

    public function test_total_registrations()
    {
        $admin = $this->setAdmin();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        $response = $this->actingAs($admin)->getJson($this->v1API(("users/total-registrations")));
        $response->assertOk();
        $this->assertDatabaseCount('users',5);
    }

    public function test_total_registrationPV()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        (new PackagePayment([
            'user_uuid' => $user1->uuid,
            'amount' => 20000,
            'point_value' => $setting->unit_point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user2->uuid,
            'amount' => 20000,
            'point_value' => $setting->unit_point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user3->uuid,
            'amount' => 20000,
            'point_value' => $setting->unit_point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();
        (new PackagePayment([
            'user_uuid' => $user4->uuid,
            'amount' => 20000,
            'point_value' => $setting->unit_point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();

        $response = $this->actingAs($admin)->getJson($this->v1API(("users/total-registration-pv")));
        $response->assertOk();
        $this->assertDatabaseCount('users',5);
        $response->assertJson(['data'=>$setting->unit_point_value * 4]);   
    }

    public function test_invite_guest()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();

        $data = [
            'email'=>"user@mail.com",
        ];

        $response = $this->actingAs($user)->postJson($this->v1API("users/{$user->uuid}/invite-guest"),$data);
        $response->assertOk();
    }

    public function test_get_user()
    {
        $admin = $this->setAdmin();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $response = $this->actingAs($admin)->getJson($this->v1API("users/{$user->uuid}/user"));
        $response->assertOk();
    }

    public function test_send_message()
    {
        $admin = $this->setAdmin();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $data = [
            'subject'=>'payment',
            'body'=>'we are expecting your payment'
        ];

        $response = $this->actingAs($admin)->PostJson($this->v1API("users/{$user->uuid}/send-message"),$data);
        $response->assertOk();
    }

    public function test_paid_users()
    {
        $admin = $this->setAdmin();
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();

        (new PackagePayment([
            'user_uuid' => $user->uuid,
            'amount' => 20000,
            'point_value' => $setting->unit_point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();

        (new PackagePayment([
            'user_uuid' => $user1->uuid,
            'amount' => 20000,
            'point_value' => $setting->unit_point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();

        $response = $this->actingAs($admin)->getJson($this->v1API("users/paid-users"));
        $response->assertOk();
        $this->assertDatabaseCount('package_payments',2);
    }

    public function test_total_paid_users()
    {
        $admin = $this->setAdmin();
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();

        (new PackagePayment([
            'user_uuid' => $user->uuid,
            'amount' => 20000,
            'point_value' => $setting->unit_point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();

        (new PackagePayment([
            'user_uuid' => $user1->uuid,
            'amount' => 20000,
            'point_value' => $setting->unit_point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();

        $response = $this->actingAs($admin)->getJson($this->v1API("users/total-paid-users"));
        $response->assertOk();
        $this->assertDatabaseCount('package_payments',2);
    }

    public function test_sum_paid_users()
    {
        $admin = $this->setAdmin();
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();

        (new PackagePayment([
            'user_uuid' => $user->uuid,
            'amount' => 20000,
            'point_value' => $setting->unit_point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();

        (new PackagePayment([
            'user_uuid' => $user1->uuid,
            'amount' => 20000,
            'point_value' => $setting->unit_point_value,
            'reference' => 'hdhdhyey34y5jeje',
            'status'=>'approved'
        ]))->save();

        $response = $this->actingAs($admin)->getJson($this->v1API("users/sum-paid-users"));
        $response->assertOk();
        $this->assertDatabaseCount('package_payments',2);
    }

    
}
