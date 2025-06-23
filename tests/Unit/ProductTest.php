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
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Product::factory()->create(['name' => null]);
    }

    public function testProductPriceIsRequired()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Product::factory()->create(['price' => null]);
    }

    public function testProductPriceIsNumeric()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Product::factory()->create(['price' => 'invalid-price']);
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
