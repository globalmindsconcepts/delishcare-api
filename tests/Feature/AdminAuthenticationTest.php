<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test create user.
     *
     * @return void
     */

    public function test_admin_login()
    {
        $this->createAdmin(1,['email'=>'admin@mail.com'])->first();

        $response = $this->postJson($this->v1API('auth/admin-login'),
        ['email'=>'admin@mail.com','password'=>'password']);

        $response->assertStatus(200);
    }

    public function test_admin_login_invilid_credential()
    {
        $this->createAdmin(1,['email'=>'larry@mail'])->first();

        $response = $this->postJson($this->v1API('auth/admin-login'),
        ['email'=>'larry@mail','password'=>'passwor']);

        $response->assertStatus(400);
    }


}
