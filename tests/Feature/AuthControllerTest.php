<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_user_422()
    {
        //$user = $this->createUsers()->first();
        $response = $this->postJson($this->v1API('auth/register'),
        ['username'=>'','password'=>'password']);

        $response->assertStatus(422);
    }

    public function test_create_user_200()
    {
        $user1 = $this->createUsers()->first();
        $user = ['first_name'=>'larry','last_name'=>'josh','email'=>'larry@mail',
        'phone'=>'090123456','username'=>'larry','referrer'=>$user1->username,
        'password'=>'password'];

        $response = $this->postJson($this->v1API('auth/register'), $user);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users',['email'=>'larry@mail']);
        $this->assertDatabaseHas('referrals', ['referrer_uuid' => $user1->uuid]);
    }

    public function test_create_user_email_is_unique()
    {
        $this->createUsers(1,['email'=>'larry@mail'])->first();
        $response = $this->postJson($this->v1API('auth/register'),
        ['first_name'=>'larry','last_name'=>'josh','email'=>'larry@mail',
        'phone'=>'090123456',
        'password'=>'password']);

        $response->assertStatus(422);
    }

    public function test_user_login()
    {
        $this->createUsers(1,['email'=>'larry@mail'])->first();

        $response = $this->postJson($this->v1API('auth/login'),
        ['email'=>'larry@mail','password'=>'password']);

        $response->assertStatus(200);
    }

    public function test_user_login_invilid_credential()
    {
        $this->createUsers(1,['email'=>'larry@mail'])->first();

        $response = $this->postJson($this->v1API('auth/login'),
        ['email'=>'larry@mail','password'=>'passwor']);

        $response->assertStatus(400);
    }

    public function test_user_registration_with_placer()
    {
        $users = $this->createUsers(2,null);
        $user1 = $users->first();
        $user2 = $users->last();
        $user = ['first_name'=>'larry','last_name'=>'josh','email'=>'larry@mail',
        'phone'=>'090123456','username'=>'larry','referrer'=>$user1->username,'placer'=>$user2->username,
        'password'=>'password'];

        $response = $this->postJson($this->v1API('auth/register'), $user);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users',['email'=>'larry@mail']);
        $this->assertDatabaseHas('referrals', ['referrer_uuid' => $user1->uuid,'placer_uuid'=>$user2->uuid]);
    }
}
