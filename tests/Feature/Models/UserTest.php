<?php

namespace Tests\Feature\Models;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\Models\ModelHelperTesting;
class UserTest extends TestCase
{
    use RefreshDatabase, ModelHelperTesting;

    protected function model(): Model
    {
        return new User();
    }

    public function testUserRelationShipWithPost()
    {
        $count = rand(1, 10);
        $user = User::factory()->hasArticles($count)->create();

        $this->assertCount($count, $user->articles);
        $this->assertTrue($user->articles->first() instanceof Article);
    }
}
