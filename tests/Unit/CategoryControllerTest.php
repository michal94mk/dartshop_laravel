<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\User;


class CategoryControllerTest extends TestCase
{
    //use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $adminUser = User::factory()->create(['role' => 'admin']);
        $this->actingAs($adminUser);
    }


    public function testIndexReturnsCategories()
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
    }

    public function testCreateCategory()
    {
        $response = $this->post(route('admin.categories.store'), [
            'name' => 'New Category Name',
        ]);
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category Name',
        ]);
    }

    public function testEditCategory()
    {
        $category = Category::factory()->create();
        $response = $this->get(route('admin.categories.edit', $category->id));
        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.edit');
    }


    public function testUpdateCategory()
    {
        $category = Category::factory()->create();
        $response = $this->put(route('admin.categories.update', $category->id), [
            'name' => 'Updated Category Name',
        ]);
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category Name',
        ]);
    }

    public function testDeleteCategory()
    {
        $category = Category::factory()->create();
        $response = $this->delete(route('admin.categories.destroy', $category->id));
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}
