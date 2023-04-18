<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_user_profile_with_photo()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        Storage::fake('profile-photos');

        $data = [
            'gender' => 'female',
            'phone'=>'09030520201',
            'address'=>'abcderft',
            'image'=>UploadedFile::fake()->image('image.png')
        ];

        $response = $this->actingAs($user)->postJson($this->v1API("users/{$user->uuid}/create-profile"),$data);

        $response->assertOk();
        $response->assertJson(['status' => 200, 'success' => true]);
        $this->assertDatabaseHas('user_profiles', ['user_uuid' => $user->uuid]);
    }

    public function test_update_user_profile_with_photo()
    {
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $this->createUserProfile(['user_uuid'=>$user->uuid]);

        Storage::fake('profile-photos');

        $data = [
            'gender' => 'female',
            'phone'=>'09030520201',
            'address'=>'abcderft',
            'image'=>UploadedFile::fake()->image('image.png')
        ];

        $response = $this->actingAs($user)->postJson($this->v1API("users/{$user->uuid}/update-profile"),$data);

        $response->assertOk();
        $response->assertJson(['status' => 200]);
        $this->assertDatabaseHas('user_profiles', ['user_uuid' => $user->uuid]);
    }

    public function test_toggle_2fa()
    {
        $admin = $this->setAdmin();
        $package = $this->createPackage(['name' => 'basic', 'vip' => 'vip1', 'point_value' => 5, 'registration_value' => 20000])->first();
        $user = $this->createUsers(1,['package_id'=>$package->id])->first();

        $this->createUserProfile(['user_uuid'=>$user->uuid]);

        $data = [
            'enable_2fa'=>true
        ];

        $response = $this->actingAs($admin)->putJson($this->v1API("users/{$user->uuid}/toggle-2fa"),$data);
        $response->assertOk();
    }
}
