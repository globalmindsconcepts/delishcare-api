<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReferralBonusSettingTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_update_referral_bonus_setting()
    {
        $setting = $this->createReferralBonusSetting()->first();
        $admin = $this->setAdmin();

        $data = ['package_id' => $setting->package_id,
        'generation_1_percentage'=>20,
        'generation_2_percentage'=>5,
        'generation_3_percentage'=>1,
        'generation_4_percentage'=>1,
        'generation_5_percentage'=>1,
        'generation_6_percentage'=>1];

        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update-referral-bonus/{$setting->id}"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('referral_bonus_settings', ['generation_1_percentage' => $data['generation_1_percentage']]);
    }
}
