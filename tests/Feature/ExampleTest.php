<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testMainIndex(): void
    {
        $response = $this->get(route('main'));
        $response->assertOk();
    }
}
