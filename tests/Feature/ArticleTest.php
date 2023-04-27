<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @group ci
 * @group article
 */
class ArticleTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     */
    protected function setUp(): void
    {

        parent::setUp();
    }

    public function testIndex(): void
    {
        Article::factory(10)->create();
        $response = $this->get(route('articles.index'));
        $response->assertOk();
    }

    public function testShow(): void
    {
        $this->article = Article::factory(1)->create()->first();
        $response = $this->get(route('articles.show', [$this->article->slug]));
        $response->assertOk();
    }
}
