<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Database\Factories\ProductFactory;
use Illuminate\Support\Facades\Validator;

class ProductTest extends TestCase
{
    public function testProductCanBeCreated()
    {
        $product = Product::factory()->create();

        $this->assertInstanceOf(Product::class, $product);
        $this->assertGreaterThan(0, $product->id);
        $this->assertNotEmpty($product->name);
        $this->assertGreaterThan(0, $product->price);
    }

    public function testProductNameIsRequired()
    {
        $product = Product::factory()->make(['name' => '']);

        $validator = Validator::make($product->toArray(), Product::$rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function testProductPriceIsRequired()
    {
        $product = Product::factory()->make(['price' => 0]);

        $validator = Validator::make($product->toArray(), Product::$rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
    }

    public function testProductPriceIsNumeric()
    {
        $product = Product::factory()->make(['price' => 'invalid-price']);

        $this->assertFalse($product->validate(['price' => 'invalid-price']));
        $this->assertFalse($product->getErrors()->has('price'));
    }

    public function testProductCanBeUpdated()
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ]);

        $product->update([
            'name' => 'Updated Product',
            'price' => 15.99,
        ]);

        $this->assertEquals('Updated Product', $product->fresh()->name);
        $this->assertEquals(15.99, $product->fresh()->price);
    }

    public function testProductCanBeDeleted()
    {
        $product = Product::factory()->create();

        $product->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
