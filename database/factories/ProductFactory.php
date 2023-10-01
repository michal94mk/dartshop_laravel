<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $category = Category::inRandomOrder()->first() ?? Category::factory()->create();
        $brand = Brand::inRandomOrder()->first() ?? Brand::factory()->create();

        return [
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ];
    }
}
