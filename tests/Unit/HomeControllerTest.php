<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsViewHome()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertViewIs('app');
    }
}
