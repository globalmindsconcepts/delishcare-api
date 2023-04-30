<?php

namespace Tests\Feature;

use App\Console\Commands\DeactivateUserCommand;
use App\Console\Commands\ProcessGlobalProfitSharing;
use App\Console\Commands\ProcessLoyaltyBonus;
use App\Console\Commands\ProcessProfitPool;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Console\Commands\ProcessEquilibriumBonus;
use App\Models\PackagePayment;

class CronCommandTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_process_equilibrium_bonus_command()
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

        $res = (new ProcessEquilibriumBonus)->handle();
        $this->assertEquals(0,$res);
    }

    public function test_process_loyalty_bonus_command()
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

        $res = (new ProcessLoyaltyBonus)->handle();
        $this->assertEquals(0,$res);
    }

    public function test_process_profit_pool_bonus_command()
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

        $res = (new ProcessProfitPool)->handle();
        $this->assertEquals(0,$res);
    }

    public function test_process_global_profit_bonus_command()
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

        $res = (new ProcessGlobalProfitSharing)->handle();
        $this->assertEquals(0,$res);
    }

    public function test_process_deactivate_user_command()
    {
        $setting = $this->createSetting()->first();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip6', 'point_value' => 5, 
        'registration_value' => 20000,'profit_pool_eligible'=>true])->first();

        $user = $this->createUsers(1,['package_id'=>$package->id])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package->id,'email'=>'email4@mail.com'])->first();

        
        (new Transaction([
            'user_uuid' => $user1->uuid,
            'amount' => 20000,
            //'point_value' => $setting->point_value,
            'txn_reference' => 'hdhdhyey34y5jeje',
            'txn_status'=>'successful',
            'narration'=>'payment',
            'txn_type'=>'credit',
            'txn_source'=>'package_payment'
        ]))->save();
        (new Transaction([
            'user_uuid' => $user2->uuid,
            'amount' => 20000,
            'txn_reference' => 'hdhdhyey34y5jeje',
            'txn_status'=>'successful',
            'narration'=>'payment',
            'txn_type'=>'credit',
            'txn_source'=>'package_payment'
        ]))->save();
        (new Transaction([
            'user_uuid' => $user3->uuid,
            'amount' => 20000,
            'txn_reference' => 'hdhdhyey34y5jeje',
            'txn_status'=>'successful',
            'narration'=>'payment',
            'txn_type'=>'credit',
            'txn_source'=>'package_payment'
        ]))->save();
        (new Transaction([
            'user_uuid' => $user4->uuid,
            'amount' => 20000,
            'txn_reference' => 'hdhdhyey34y5jeje',
            'txn_status'=>'successful',
            'narration'=>'payment',
            'txn_type'=>'credit',
            'txn_source'=>'package_payment'
        ]))->save();

        $res = (new DeactivateUserCommand)->handle();
        $this->assertEquals(0,$res);

        $this->assertDatabaseMissing('users',['uuid'=>$user->uuid]);
    }
}
