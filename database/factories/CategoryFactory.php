<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            // categories.name is unique and CategoryRequest restricts it to letters, digits,
            // spaces, "-", "." and "&" - so the suffix carries uniqueness, not faker->word
            'name' => ucfirst($this->faker->word()) . ' ' . $this->faker->unique()->numberBetween(1, 999999),
        ];
    }
}
