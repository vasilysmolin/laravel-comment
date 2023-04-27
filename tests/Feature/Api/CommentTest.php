<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @group ci
 * @group article
 */
class CommentTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    /**
     * A basic test example.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testStore(): void
    {
        $article = Article::factory(1)->create()->first();
        $user = User::factory(1)->create()->first();
        $response = $this->json('POST', route('comments.store'), [
            'text' => $this->faker->text,
            'article_id' => $article->getKey(),
            'user_id' => $user->getKey(),
        ]);
        $response->assertCreated();
    }

}
