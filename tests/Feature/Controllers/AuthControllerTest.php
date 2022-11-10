<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class AuthControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;


    public function testRegisterUser()
    {

        \Artisan::call('passport:install');
        $password = 'password';
        $passwordHash = Hash::make($password);
        $user = User::factory()->make(['password' => $passwordHash]);
        $token = $user->generateToken();
        $response = $this->postJson(
            'api/v1/user-register',
            [
                'email' => 'Sample' . $user->email,
                'password' => $password,
                "name" => $user->name,
                "email" => $user->email,
                "password" => $user->password,
                "password_confirmation" => $user->password,
                "mobile_number" => '9' . $user->mobile_number,
                'token' => $token
            ]
        );
        // dd($response['success']['name']);
        $response->assertStatus(201);
        $response
            ->assertJson([
                'success' => [
                    'token' => $response['success']['token'],
                    'name' => $response['success']['name'],
                ]
            ]);
    }

    public function testLoginUser()
    {
        \Artisan::call('passport:install');
        $password = 'password';
        $passwordHash = Hash::make($password);
        $user = User::factory()->create(['password' => $passwordHash]);
        $user->tokens()->delete();
        $token = $user->generateToken();
        $response = $this->postJson(
            'api/v1/user-login',
            ['email' => $user->email, 'password' => $password, 'token' => $token]
        );
        $response->assertStatus(202);
        $response
            ->assertJson([
                'success' => [
                    'token' => $response['success']['token'],

                    'user' => [
                        'id' =>  $response['success']['user']['id'],
                        'name' => $response['success']['user']['name'],
                        'email' => $response['success']['user']['email'],
                        'type' => $response['success']['user']['type'],
                        'mobile_number' => $response['success']['user']['mobile_number'],
                        'created_at' => $response['success']['user']['created_at'],
                        'updated_at' => $response['success']['user']['updated_at'],
                    ]
                ]
            ]);
    }
}
