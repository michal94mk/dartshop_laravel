<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsViewHome()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertViewIs('home');
    }
}
