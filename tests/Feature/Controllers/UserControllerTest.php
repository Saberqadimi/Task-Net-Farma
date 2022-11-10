<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndexMethod()
    {
        // $this->withoutExceptionHandling();

        $users = $this->usersForIndexMethod();

        $response = $this->getJson(route('api.user.index'),  $users->toArray());
        $response->assertOk();
        $response->assertStatus(200);
        $response->getContent();
    }



    public function testCreateMethod()
    {

        $user = $this->userForRequestPassport();
        $token = $user->generateToken();
        $response = $this->postJson(
            route('api.user.store'),
            [
                "name" => $user->name,
                'email' => rand(1, 3000) . $user->email,
                'password' => $user->password,
                "password_confirmation" => $user->password,
                "mobile_number" => rand(100, 3450) . $user->mobile_number,
                'token' => $token
            ]
        );

        $response->assertStatus(201);
        $response
            ->assertJson([
                'success' => [
                    'token' => $response['success']['token'],
                    'name' => $response['success']['name'],
                ]
            ]);
    }


    public function testSingleMethod()
    {

        $user = User::factory()->create();
        $response = $this->getJson(route('api.user.single', $user->id));
        $response->assertOk();
        $response->assertStatus(200);
        $response
            ->assertJson([
                'data' => [
                    "id" => $user->id,
                    "name" =>  $user->name,
                    "email" => $user->email,
                    "type" => $user->type,
                    "mobile_number" => $user->mobile_number,
                ],
            ]);
    }


    public function testUpdateMethod()
    {
        $this->withoutExceptionHandling();

        \Artisan::call('passport:install');
        $password = 'password';
        $passwordHash = Hash::make($password);
        $user = User::factory()->create(['password' => $passwordHash]);
        $token = $user->generateToken();
        $response = $this->patchJson(
            route('api.user.update', $user->id),
            [
                "name" => 'admin-tes user',
                'email' => 'adminTest@gmail.com',
                'password' => $passwordHash,
                "mobile_number" => '09123214569',
                'token' => $token
            ]
        );

        $response->assertStatus(201);
        $response
            ->assertJson([
                'success' => [
                    'token' => $response['success']['token'],
                    'name' => $response['success']['name'],
                ]
            ]);
        $user->delete();
    }


    public function testDestroyMethod()
    {
        $user = $this->userDeleteForRequestPassport();
        $token = $user->generateToken();

        $response = $this->deleteJson(route('api.user.delete', $user->id));

        $this->assertDeleted($user)->assertEmpty($user->tokens);
        $response->assertStatus(200);
        $response
            ->assertJson([
                "status" => 200,
                "data" => []
            ]);
    }



    /**helper methods for test */

    public function usersForIndexMethod()
    {
        $password = 'password';
        $passwordHash = Hash::make($password);
        $users = User::factory()->count(20)->create(['password' => $passwordHash]);

        return $users;
    }


    public function userForRequestPassport()
    {
        \Artisan::call('passport:install');
        $password = 'password';
        $passwordHash = Hash::make($password);
        $user = User::factory()->make(['password' => $passwordHash]);

        return $user;
    }
    public function userDeleteForRequestPassport()
    {
        \Artisan::call('passport:install');
        $password = 'password';
        $passwordHash = Hash::make($password);
        $user = User::factory()->create(['password' => $passwordHash]);

        return $user;
    }
}
