<?php

namespace Tests\Feature\Controllers;

use App\Http\Resources\Api\V1\ArticleCollection;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndexMethod()
    {
        $user = $this->userForRequestPassport();
        $token = $user->generateToken();
        $headers = ['Authorization' => 'Bearer $token'];

        $posts = Article::factory()->count(20)->create();
        $response = $this->getJson(route('api.post.index'), $headers, $posts);
        $response->assertStatus(200);
        $response->getContent();
    }


    public function testStoreMethod()
    {
        $user = $this->userForRequestPassport();
        $token = $user->generateToken();
        $headers = ['Authorization' => 'Bearer $token'];
        $article = [
            'title' => 'this is title for test article',
            'description' => 'this is description for test article',
            'image' => 'https://static.remove.bg/remove-bg-web/221525818b4ba04e9088d39cdcbd0c7bcdfb052e/assets/start-1abfb4fe2980eabfbbaaa4365a0692539f7cd2725f324f904565a9a744f8e214.jpg',
            'user_id' => $user->id
        ];
        $response = $this->postJson(route('api.post.store'), $article, $headers);

        $response->assertStatus(200);
        $response
            ->assertJson([
                'data' => $article
            ]);
    }


    public function testSingleMethod()
    {
        // $this->withoutExceptionHandling();

        $user = $this->userForRequestPassport();
        $token = $user->generateToken();
        $headers = ['Authorization' => 'Bearer $token'];
        $article = Article::factory()->create();
        $response = $this->getJson(route('api.post.single', $article->id), $headers);

        $response->assertOk();
        $response->assertStatus(200);
        $response
            ->assertJson([
                'data' => [
                    "id" => $article->id,
                    "user_id" => $article->user_id,
                    "title" => $article->title,
                    "image" => $article->image,
                    "description" => $article->description,
                ],

            ]);
    }

    public function testUpdateMethod()
    {
        $user = $this->userForRequestPassport();
        $token = $user->generateToken();
        $headers = ['Authorization' => 'Bearer $token'];
        $article = Article::factory()->create();
        $articlenew = [
            'title' => 'this title for update article',
            'description' => 'this is description for test article',
            'image' => 'https://static.remove.bg/remove-bg-web/221525818b4ba04e9088d39cdcbd0c7bcdfb052e/assets/start-1abfb4fe2980eabfbbaaa4365a0692539f7cd2725f324f904565a9a744f8e214.jpg',
            'user_id' => $user->id
        ];
        $response = $this->patchJson(route('api.post.update', $article->id), $articlenew, $headers);

        $response->assertStatus(200);
        $response
            ->assertJson([
                "data" => true,
                "success" => "مقاله شما با موفقیت آپدیت شد."
            ]);
        $article->delete();
    }


    public function testDestroyMethod()
    {
        $user = $this->userForRequestPassport();
        $token = $user->generateToken();
        $headers = ['Authorization' => 'Bearer $token'];
        $article = Article::factory()->create();

        $response = $this->deleteJson(route('api.post.delete', $article->id), $headers);

        $this
            ->assertDeleted($article);
        $response->assertStatus(200);
        $response
            ->assertJson([
                "status" => 200,
                "data" => []
            ]);
    }


    public function userForRequestPassport()
    {
        \Artisan::call('passport:install');
        $password = 'password';
        $passwordHash = Hash::make($password);
        $user = User::factory()->create(['password' => $passwordHash]);
        Passport::actingAs($user);

        return $user;
    }
}
