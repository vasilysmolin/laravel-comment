<?php

namespace Tests\Feature;

use App\Models\Ad;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @group ci
 * @group article
 */
class AdTest extends TestCase
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
        Ad::factory(10)->create();
        $response = $this->get(route('ad.index'));
        $response->assertOk();
    }

    public function testShow(): void
    {
        $this->article = Ad::factory(1)->create()->first();
        $response = $this->get(route('ad.show', [$this->article->slug]));
        $response->assertOk();
    }
}
