<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Brand;
use App\Http\Requests\Admin\BrandRequest;
use Illuminate\Support\Facades\Validator;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    public function testBrandCanBeCreated()
    {
        $brand = Brand::factory()->create();

        $this->assertInstanceOf(Brand::class, $brand);
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => $brand->name,
        ]);
    }

    public function testBrandNameIsRequired()
    {
        $request = new BrandRequest();
        $rules = $request->rules();
        
        $validator = Validator::make(['name' => ''], $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function testBrandNameIsUnique()
    {
        $existingBrand = Brand::factory()->create();
        
        $request = new BrandRequest();
        $rules = $request->rules();
        
        $validator = Validator::make(['name' => $existingBrand->name], $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function testBrandNameIsUniqueIgnoringItself()
    {
        $existingBrand = Brand::factory()->create();
        
        // Create a mock request with route parameter
        $request = BrandRequest::create('/api/admin/brands/' . $existingBrand->id, 'PUT');
        $request->setRouteResolver(function () use ($existingBrand) {
            $route = new class {
                public function parameter($param, $default = null) {
                    return $this->brand;
                }
                public $brand;
            };
            $route->brand = $existingBrand;
            return $route;
        });
        $rules = $request->rules();
        
        $validator = Validator::make(['name' => $existingBrand->name], $rules);

        $this->assertTrue($validator->passes());
        $this->assertFalse($validator->fails());
    }

    public function testBrandCanBeUpdated()
    {
        $brand = Brand::factory()->create();

        $brand->update([
            'name' => 'Updated Brand',
        ]);

        $this->assertEquals('Updated Brand', $brand->fresh()->name);
    }

    public function testBrandCanBeDeleted()
    {
        $brand = Brand::factory()->create();

        $brand->delete();

        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }
}
