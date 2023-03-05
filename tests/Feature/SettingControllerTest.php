<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_update_welcome_bonus_percentage()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['welcome_bonus_percentage' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['welcome_bonus_percentage' => $data['welcome_bonus_percentage']]);
    }

    public function test_update_unit_point_value()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['unit_point_value' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['unit_point_value' => $data['unit_point_value']]);
    }

    public function test_equillibrum_bonus()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['equillibrum_bonus' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['equillibrum_bonus' => $data['equillibrum_bonus']]);
    }

    public function test_loyalty_bonus_percentage()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['loyalty_bonus_percentage' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['loyalty_bonus_percentage' => $data['loyalty_bonus_percentage']]);
    }

    public function test_profit_pool_percentage()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['profit_pool_percentage' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['profit_pool_percentage' => $data['profit_pool_percentage']]);
    }

    public function test_profit_pool_duration()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['profit_pool_duration' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['profit_pool_duration' => $data['profit_pool_duration']]);
    }

    public function test_profit_days_offset()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['profit_pool_days_offset' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['profit_pool_days_offset' => $data['profit_pool_days_offset']]);
    }

    public function test_profit_num_downlines()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['profit_pool_num_of_downlines' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['profit_pool_num_of_downlines' => $data['profit_pool_num_of_downlines']]);
    }

    public function test_minimum_withdrawal()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['minimum_withdrawal' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['minimum_withdrawal' => $data['minimum_withdrawal']]);
    }

    public function test_maximum_withdrawal()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['maximum_withdrawal' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['maximum_withdrawal' => $data['maximum_withdrawal']]);
    }

    public function test_global_profit_first_percentage()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['global_profit_first_percentage' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['global_profit_first_percentage' => $data['global_profit_first_percentage']]);
    }

    public function test_global_profit_second_percentage()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['global_profit_second_percentage' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['global_profit_second_percentage' => $data['global_profit_second_percentage']]);
    }

    public function test_next_global_profit_share_month()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['next_global_profit_share_month' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['next_global_profit_share_month' => $data['next_global_profit_share_month']]);
    }

    public function test_next_global_profit_share_day()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['next_global_profit_share_day' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['next_global_profit_share_day' => $data['next_global_profit_share_day']]);
    }

    public function test_placement_bonus_percentage()
    {
        $setting = $this->createSetting()->first();
        $admin = $this->setAdmin();

        $data = ['placement_bonus_percentage' => 10];
        $response = $this->actingAs($admin)->putJson($this->v1API("settings/update"), $data);
        $response->assertOk();
        $this->assertDatabaseHas('settings', ['placement_bonus_percentage' => $data['placement_bonus_percentage']]);
    }


}
