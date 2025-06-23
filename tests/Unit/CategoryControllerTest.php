<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\User;


class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Create admin role
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        
        $adminUser = User::factory()->create(['is_admin' => true]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
    }


    public function testIndexReturnsCategories()
    {
        $response = $this->getJson('/api/admin/categories');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'current_page', 'total']);
    }

    public function testCreateCategory()
    {
        $response = $this->postJson('/api/admin/categories', [
            'name' => 'New Category Name',
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'message', 'data']);
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category Name',
        ]);
    }

    public function testEditCategory()
    {
        $category = Category::factory()->create();
        $response = $this->getJson("/api/admin/categories/{$category->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'name']);
    }


    public function testUpdateCategory()
    {
        $category = Category::factory()->create();
        $response = $this->putJson("/api/admin/categories/{$category->id}", [
            'name' => 'Updated Category Name',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data']);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category Name',
        ]);
    }

    public function testDeleteCategory()
    {
        $category = Category::factory()->create();
        $response = $this->deleteJson("/api/admin/categories/{$category->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}
