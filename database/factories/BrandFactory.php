<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Brand;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition()
    {
        return [
            // brands.name is unique and BrandRequest restricts it to letters, digits,
            // spaces, "-", "." and "&" - so the suffix carries uniqueness, not faker->word
            'name' => ucfirst($this->faker->word()) . ' ' . $this->faker->unique()->numberBetween(1, 999999),
        ];
    }
}
