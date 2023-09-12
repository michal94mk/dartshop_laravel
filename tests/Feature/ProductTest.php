<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;



class ProductTest extends TestCase
{
    public function testProductCanBeCreated()
    {
        $category = Category::create([
            'name' => 'Sample Category',
        ]);

        $brand = Brand::create([
            'name' => 'Sample Brand',
        ]);

        $product = new Product([
            'name' => 'Sample Product',
            'price' => 10.99,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ]);

        $product->category()->associate($category);
        $product->brand()->associate($brand);

        $this->assertTrue($product->save());
    }

    public function testProductPriceIsNotNumeric()
{
    $category = Category::create([
        'name' => 'Sample Category',
    ]);

    $brand = Brand::create([
        'name' => 'Sample Brand',
    ]);

    $product = new Product([
        'name' => 'Sample Product',
        'price' => 'invalid-price',
        'category_id' => $category->id,
        'brand_id' => $brand->id,
    ]);

    $validator = Validator::make($product->toArray(), Product::rules());

    $this->assertTrue($validator->fails());
}
}
