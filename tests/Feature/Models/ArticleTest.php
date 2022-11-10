<?php

namespace Tests\Feature\Models;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\Models\ModelHelperTesting;

class ArticleTest extends TestCase
{
    use RefreshDatabase, ModelHelperTesting;

    protected function model(): Model
    {
        return new Article();
    }

    public function testPostRelationshipWithUser()
    {
        $article = Article::factory()
            ->for(User::factory())
            ->create();

        $this->assertTrue(isset($article->user->id));
        $this->assertTrue($article->user instanceof User);
    }
}
