<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryTest extends TestCase
{
    //use RefreshDatabase;

    public function testCategoryCanBeCreated()
    {
        $category = Category::factory()->create();

        $this->assertInstanceOf(Category::class, $category);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    public function testCategoryNameIsRequired()
    {
        $category = Category::factory()->make(['name' => '']);

        $validator = Validator::make($category->toArray(), Category::rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function testCategoryCanBeUpdated()
    {
        $category = Category::factory()->create();

        $category->update(['name' => 'Updated Category']);

        $this->assertEquals('Updated Category', $category->fresh()->name);
    }

    public function testCategoryCanBeDeleted()
    {
        $category = Category::factory()->create();

        $category->delete();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
