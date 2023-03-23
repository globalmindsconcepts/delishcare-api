<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\GenealogyService;
use Illuminate\Support\Facades\DB;
use App\Models\ReferralBonus;

class GenealogyServiceTest extends TestCase
{
    use RefreshDatabase;
    private $genealogyService;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_one_child()
    {
        $package1 = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 
        'registration_value' => 20000])->first();
        $package2 = $this->createPackage(['name' => 'business', 'vip' => 'vip2', 'point_value' => 10, 
        'registration_value' => 30000])->first();
        $package3 = $this->createPackage(['name' => 'executive', 'vip' => 'vip3', 'point_value' => 20, 
        'registration_value' => 40000])->first();
        $package4 = $this->createPackage(['name' => 'executive', 'vip' => 'vip4', 'point_value' => 30, 
        'registration_value' => 40000])->first();

        $user1 = $this->createUsers(1,['package_id'=>$package1->id,'email'=>'jo1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package2->id,'email'=>'jo2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package3->id,'email'=>'jo3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo4@mail.com'])->first();

        $this->createReferralBonusSetting(['package_id' => $package1->id]);
        $this->createSetting(['unit_point_value' => 3500]);

        $child1 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user2->uuid]);
        //$child2 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user3->uuid]);
        //$child3 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user4->uuid]);

        (new GenealogyService)->makeReferrerAParent($user1->uuid,$user4->uuid);
        $this->assertDatabaseHas('children', ['child_id' => $user4->uuid]);
        $this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user1->uuid,'generation'=>'generation_1']);
        //$this->assertDatabaseHas('grandchildren', ['child_id' => $user4->uuid,'grandparent_id'=>$user1->uuid]);
    } 
    
    public function test_user_referral_with_placement()
    {
        $package1 = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 
        'registration_value' => 20000])->first();
        $package2 = $this->createPackage(['name' => 'business', 'vip' => 'vip2', 'point_value' => 10, 
        'registration_value' => 30000])->first();
        $package3 = $this->createPackage(['name' => 'executive', 'vip' => 'vip3', 'point_value' => 20, 
        'registration_value' => 40000])->first();
        $package4 = $this->createPackage(['name' => 'executive', 'vip' => 'vip4', 'point_value' => 30, 
        'registration_value' => 40000])->first();

        $user1 = $this->createUsers(1,['package_id'=>$package1->id,'email'=>'jo1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package2->id,'email'=>'jo2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package3->id,'email'=>'jo3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo4@mail.com'])->first();

        $this->createReferralBonusSetting(['package_id' => $package1->id]);
        $this->createReferralBonusSetting(['package_id' => $package2->id]);
        $this->createReferralBonusSetting(['package_id' => $package3->id]);
        $this->createReferralBonusSetting(['package_id' => $package4->id]);

        $this->createSetting(['unit_point_value' => 3500]);
        $this->createSetting(['placement_bonus_percentage' => 25]);

        $child1 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user2->uuid]);
        $child2 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user3->uuid]);
        //$child3 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user4->uuid]);

        (new GenealogyService)->makeReferrerAParent($user2->uuid,$user4->uuid,$user1->uuid);
        $this->assertDatabaseHas('children', ['child_id' => $user4->uuid,'parent_id'=>$user2->uuid]);
        $this->assertDatabaseHas('grandchildren', ['grandchild_id' => $user4->uuid,'grandparent_id'=>$user1->uuid]);
        $this->assertDatabaseHas('placement_bonuses', ['user_uuid' => $user1->uuid]);
        
    } 

    public function test_user_referral_without_placement()
    {
        $package1 = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 
        'registration_value' => 20000])->first();
        $package2 = $this->createPackage(['name' => 'business', 'vip' => 'vip2', 'point_value' => 10, 
        'registration_value' => 30000])->first();
        $package3 = $this->createPackage(['name' => 'executive', 'vip' => 'vip3', 'point_value' => 20, 
        'registration_value' => 40000])->first();
        $package4 = $this->createPackage(['name' => 'executive', 'vip' => 'vip4', 'point_value' => 30, 
        'registration_value' => 40000])->first();

        $user1 = $this->createUsers(1,['package_id'=>$package1->id,'email'=>'jo1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package2->id,'email'=>'jo2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package3->id,'email'=>'jo3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo4@mail.com'])->first();

        $this->createReferralBonusSetting(['package_id' => $package1->id]);
        $this->createReferralBonusSetting(['package_id' => $package2->id]);
        $this->createReferralBonusSetting(['package_id' => $package3->id]);
        $this->createReferralBonusSetting(['package_id' => $package4->id]);

        $this->createSetting(['unit_point_value' => 3500]);
        $this->createSetting(['placement_bonus_percentage' => 25]);

        $child1 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user2->uuid]);
        $child2 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user3->uuid]);
        //$child3 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user4->uuid]);

        (new GenealogyService)->makeReferrerAParent($user2->uuid,$user4->uuid);

        $this->assertDatabaseHas('children', ['child_id' => $user4->uuid,'parent_id'=>$user2->uuid]);
        $this->assertDatabaseHas('grandchildren', ['grandchild_id' => $user4->uuid,'grandparent_id'=>$user1->uuid]);
        $this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user2->uuid,'generation'=>'generation_1']);
        $this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user1->uuid,'generation'=>'generation_2']);
        
    }
    
    public function test_user_referral_generation_3()
    {
        $package1 = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 
        'registration_value' => 20000])->first();
        $package2 = $this->createPackage(['name' => 'business', 'vip' => 'vip2', 'point_value' => 10, 
        'registration_value' => 30000])->first();
        $package3 = $this->createPackage(['name' => 'executive', 'vip' => 'vip3', 'point_value' => 20, 
        'registration_value' => 40000])->first();
        $package4 = $this->createPackage(['name' => 'executive', 'vip' => 'vip4', 'point_value' => 30, 
        'registration_value' => 40000])->first();

        $user1 = $this->createUsers(1,['package_id'=>$package1->id,'email'=>'jo1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package2->id,'email'=>'jo2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package3->id,'email'=>'jo3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo4@mail.com'])->first();
        $user5 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo5@mail.com'])->first();
        $user6 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo6@mail.com'])->first();
        $user7 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo7@mail.com'])->first();
        $user8 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo8@mail.com'])->first();
        $user9 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo9@mail.com'])->first();

        $this->createReferralBonusSetting(['package_id' => $package1->id]);
        $this->createReferralBonusSetting(['package_id' => $package2->id]);
        $this->createReferralBonusSetting(['package_id' => $package3->id]);
        $this->createReferralBonusSetting(['package_id' => $package4->id]);

        $this->createSetting(['unit_point_value' => 3500]);
        $this->createSetting(['placement_bonus_percentage' => 25]);

        $child1 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user2->uuid]);
        $child2 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user3->uuid]);
        $child3 = $this->createChild(['parent_id' => $user2->uuid, 'child_id' => $user4->uuid]);
        $child4 = $this->createChild(['parent_id' => $user2->uuid, 'child_id' => $user5->uuid]);
        $child5 = $this->createChild(['parent_id' => $user3->uuid, 'child_id' => $user6->uuid]);
        $child6 = $this->createChild(['parent_id' => $user3->uuid, 'child_id' => $user7->uuid]);


        (new GenealogyService)->makeReferrerAParent($user4->uuid,$user8->uuid);
        
        $this->assertDatabaseHas('children', ['child_id' => $user8->uuid,'parent_id'=>$user4->uuid]);
        $this->assertDatabaseHas('great_grandchildren', ['great_grandchild_id' => $user8->uuid,'great_grandparent_id'=>$user1->uuid]);
        $this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user4->uuid,'generation'=>'generation_1']);
        $this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user2->uuid,'generation'=>'generation_2']);
        $this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user1->uuid,'generation'=>'generation_3']);

        (new GenealogyService)->makeReferrerAParent($user4->uuid,$user9->uuid);
        $count = ReferralBonus::where(['user_uuid' => $user1->uuid,'generation'=>'generation_3'])->get();
        //info('c', [$count]);
        $this->assertCount(2, $count);
        //$this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user1->uuid,'generation'=>'generation_3']);
    }

    public function test_user_referral_generation_4()
    {
        $package1 = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 
        'registration_value' => 20000])->first();
        $package2 = $this->createPackage(['name' => 'business', 'vip' => 'vip2', 'point_value' => 10, 
        'registration_value' => 30000])->first();
        $package3 = $this->createPackage(['name' => 'executive', 'vip' => 'vip3', 'point_value' => 20, 
        'registration_value' => 40000])->first();
        $package4 = $this->createPackage(['name' => 'executive', 'vip' => 'vip4', 'point_value' => 30, 
        'registration_value' => 40000])->first();

        $user1 = $this->createUsers(1,['package_id'=>$package1->id,'email'=>'jo1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package2->id,'email'=>'jo2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package3->id,'email'=>'jo3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo4@mail.com'])->first();
        $user5 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo5@mail.com'])->first();
        $user6 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo6@mail.com'])->first();
        $user7 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo7@mail.com'])->first();
        $user8 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo8@mail.com'])->first();
        $user9 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo9@mail.com'])->first();
        $user10 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo10@mail.com'])->first();
        $user11 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo11@mail.com'])->first();
        $user12 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo12@mail.com'])->first();
        $user13 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo13@mail.com'])->first();
        $user14 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo14@mail.com'])->first();
        $user15 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo15@mail.com'])->first();
        $user16 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo16@mail.com'])->first();

        $this->createReferralBonusSetting(['package_id' => $package1->id]);
        $this->createReferralBonusSetting(['package_id' => $package2->id]);
        $this->createReferralBonusSetting(['package_id' => $package3->id]);
        $this->createReferralBonusSetting(['package_id' => $package4->id]);

        $this->createSetting(['unit_point_value' => 3500]);
        $this->createSetting(['placement_bonus_percentage' => 25]);

        $child1 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user2->uuid]);
        $child2 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user3->uuid]);
        $child3 = $this->createChild(['parent_id' => $user2->uuid, 'child_id' => $user4->uuid]);
        $child4 = $this->createChild(['parent_id' => $user2->uuid, 'child_id' => $user5->uuid]);
        $child5 = $this->createChild(['parent_id' => $user3->uuid, 'child_id' => $user6->uuid]);
        $child6 = $this->createChild(['parent_id' => $user3->uuid, 'child_id' => $user7->uuid]);

        $this->createChild(['parent_id' => $user4->uuid, 'child_id' => $user8->uuid]);
        $this->createChild(['parent_id' => $user4->uuid, 'child_id' => $user9->uuid]);
        $this->createChild(['parent_id' => $user5->uuid, 'child_id' => $user10->uuid]);
        $this->createChild(['parent_id' => $user5->uuid, 'child_id' => $user11->uuid]);
        $this->createChild(['parent_id' => $user6->uuid, 'child_id' => $user12->uuid]);
        $this->createChild(['parent_id' => $user6->uuid, 'child_id' => $user13->uuid]);
        $this->createChild(['parent_id' => $user7->uuid, 'child_id' => $user14->uuid]);
        $this->createChild(['parent_id' => $user7->uuid, 'child_id' => $user15->uuid]);

        $child5 = $this->createChild(['parent_id' => $user3->uuid, 'child_id' => $user6->uuid]);
        $child6 = $this->createChild(['parent_id' => $user3->uuid, 'child_id' => $user7->uuid]);

        (new GenealogyService)->makeReferrerAParent($user8->uuid,$user16->uuid);
        
        $this->assertDatabaseHas('children', ['child_id' => $user16->uuid,'parent_id'=>$user8->uuid]);
        //$this->assertDatabaseHas('great_grandchildren', ['great_grandchild_id' => $user8->uuid,'great_grandparent_id'=>$user1->uuid]);
        //$this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user4->uuid,'generation'=>'generation_1']);
        //$this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user2->uuid,'generation'=>'generation_2']);
        $this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user1->uuid,'generation'=>'generation_4']);
    }

    private function test_user_referral_generation_5()
    {
        $package1 = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 
        'registration_value' => 20000])->first();
        $package2 = $this->createPackage(['name' => 'business', 'vip' => 'vip2', 'point_value' => 10, 
        'registration_value' => 30000])->first();
        $package3 = $this->createPackage(['name' => 'executive', 'vip' => 'vip3', 'point_value' => 20, 
        'registration_value' => 40000])->first();
        $package4 = $this->createPackage(['name' => 'executive', 'vip' => 'vip4', 'point_value' => 30, 
        'registration_value' => 40000])->first();

        $user0 = $this->createUsers(1,['package_id'=>$package1->id,'email'=>'jo0@mail.com'])->first();
        $user1 = $this->createUsers(1,['package_id'=>$package1->id,'email'=>'jo1@mail.com'])->first();
        $user2 = $this->createUsers(1,['package_id'=>$package2->id,'email'=>'jo2@mail.com'])->first();
        $user3 = $this->createUsers(1,['package_id'=>$package3->id,'email'=>'jo3@mail.com'])->first();
        $user4 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo4@mail.com'])->first();
        $user5 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo5@mail.com'])->first();
        $user6 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo6@mail.com'])->first();
        $user7 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo7@mail.com'])->first();
        $user8 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo8@mail.com'])->first();
        $user9 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo9@mail.com'])->first();
        $user10 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo10@mail.com'])->first();
        $user11 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo11@mail.com'])->first();
        $user12 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo12@mail.com'])->first();
        $user13 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo13@mail.com'])->first();
        $user14 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo14@mail.com'])->first();
        $user15 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo15@mail.com'])->first();
        $user16 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo16@mail.com'])->first();

        $user17 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo17@mail.com'])->first();
        $user18 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo18@mail.com'])->first();
        $user19 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo19@mail.com'])->first();
        $user20 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo20@mail.com'])->first();
        $user21 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo21@mail.com'])->first();
        $user22 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo22@mail.com'])->first();
        $user23 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo23@mail.com'])->first();
        $user24 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo24@mail.com'])->first();

        $user25 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo25@mail.com'])->first();
        $user26 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo126@mail.com'])->first();
        $user27 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo27@mail.com'])->first();
        $user28 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo28@mail.com'])->first();
        $user29 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo29@mail.com'])->first();
        $user30 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo30@mail.com'])->first();
        $user31 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo31@mail.com'])->first();
        $user32 = $this->createUsers(1,['package_id'=>$package4->id,'email'=>'jo32@mail.com'])->first();

        $this->createReferralBonusSetting(['package_id' => $package1->id]);
        $this->createReferralBonusSetting(['package_id' => $package2->id]);
        $this->createReferralBonusSetting(['package_id' => $package3->id]);
        $this->createReferralBonusSetting(['package_id' => $package4->id]);

        $this->createSetting(['unit_point_value' => 3500]);
        $this->createSetting(['placement_bonus_percentage' => 25]);

        $this->createChild(['parent_id' => $user0->uuid, 'child_id' => $user1->uuid]);

        $child1 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user2->uuid]);
        $child2 = $this->createChild(['parent_id' => $user1->uuid, 'child_id' => $user3->uuid]);
        $child3 = $this->createChild(['parent_id' => $user2->uuid, 'child_id' => $user4->uuid]);
        $child4 = $this->createChild(['parent_id' => $user2->uuid, 'child_id' => $user5->uuid]);
        $child5 = $this->createChild(['parent_id' => $user3->uuid, 'child_id' => $user6->uuid]);
        $child6 = $this->createChild(['parent_id' => $user3->uuid, 'child_id' => $user7->uuid]);

        $this->createChild(['parent_id' => $user4->uuid, 'child_id' => $user8->uuid]);
        $this->createChild(['parent_id' => $user4->uuid, 'child_id' => $user9->uuid]);
        $this->createChild(['parent_id' => $user5->uuid, 'child_id' => $user10->uuid]);
        $this->createChild(['parent_id' => $user5->uuid, 'child_id' => $user11->uuid]);
        $this->createChild(['parent_id' => $user6->uuid, 'child_id' => $user12->uuid]);
        $this->createChild(['parent_id' => $user6->uuid, 'child_id' => $user13->uuid]);
        $this->createChild(['parent_id' => $user7->uuid, 'child_id' => $user14->uuid]);
        $this->createChild(['parent_id' => $user7->uuid, 'child_id' => $user15->uuid]);

        $this->createChild(['parent_id' => $user8->uuid, 'child_id' => $user16->uuid]);
        $this->createChild(['parent_id' => $user8->uuid, 'child_id' => $user17->uuid]);
        $this->createChild(['parent_id' => $user9->uuid, 'child_id' => $user18->uuid]);
        $this->createChild(['parent_id' => $user9->uuid, 'child_id' => $user19->uuid]);
        $this->createChild(['parent_id' => $user10->uuid, 'child_id' => $user20->uuid]);
        $this->createChild(['parent_id' => $user10->uuid, 'child_id' => $user21->uuid]);
        $this->createChild(['parent_id' => $user11->uuid, 'child_id' => $user22->uuid]);
        $this->createChild(['parent_id' => $user11->uuid, 'child_id' => $user23->uuid]);

        $this->createChild(['parent_id' => $user12->uuid, 'child_id' => $user24->uuid]);
        $this->createChild(['parent_id' => $user12->uuid, 'child_id' => $user25->uuid]);
        $this->createChild(['parent_id' => $user13->uuid, 'child_id' => $user26->uuid]);
        $this->createChild(['parent_id' => $user13->uuid, 'child_id' => $user27->uuid]);
        $this->createChild(['parent_id' => $user14->uuid, 'child_id' => $user28->uuid]);
        $this->createChild(['parent_id' => $user14->uuid, 'child_id' => $user29->uuid]);
        $this->createChild(['parent_id' => $user11->uuid, 'child_id' => $user22->uuid]);
        $this->createChild(['parent_id' => $user11->uuid, 'child_id' => $user23->uuid]);

        (new GenealogyService)->makeReferrerAParent($user12->uuid,$user24->uuid);
        
        $this->assertDatabaseHas('children', ['child_id' => $user24->uuid,'parent_id'=>$user12->uuid]);
        info('uuid', [$user1->uuid]);
        //BIqbh4Pp7H3 $this->assertDatabaseHas('great_grandchildren', ['great_grandchild_id' => $user8->uuid,'great_grandparent_id'=>$user1->uuid]);
        //$this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user4->uuid,'generation'=>'generation_1']);
        //$this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user2->uuid,'generation'=>'generation_2']);
        $this->assertDatabaseHas('referral_bonuses', ['user_uuid' => $user0->uuid,'generation'=>'generation_5']);
    }

    public function test_network_structure()
    {
        
    }
}
