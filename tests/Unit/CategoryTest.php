<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Http\Requests\Admin\CategoryRequest;
use Illuminate\Support\Facades\Validator;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

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
        $request = new CategoryRequest();
        $rules = $request->rules();
        
        $validator = Validator::make(['name' => ''], $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function testCategoryNameIsUnique()
    {
        $existingCategory = Category::factory()->create();
        
        $request = new CategoryRequest();
        $rules = $request->rules();
        
        $validator = Validator::make(['name' => $existingCategory->name], $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function testCategoryNameIsUniqueIgnoringItself()
    {
        $existingCategory = Category::factory()->create();
        
        // Create a mock request with route parameter
        $request = CategoryRequest::create('/api/admin/categories/' . $existingCategory->id, 'PUT');
        $request->setRouteResolver(function () use ($existingCategory) {
            $route = new class {
                public function parameter($param, $default = null) {
                    return $this->category;
                }
                public $category;
            };
            $route->category = $existingCategory;
            return $route;
        });
        $rules = $request->rules();
        
        $validator = Validator::make(['name' => $existingCategory->name], $rules);

        $this->assertTrue($validator->passes());
        $this->assertFalse($validator->fails());
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
