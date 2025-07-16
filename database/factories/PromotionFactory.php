<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'name' => $this->faker->unique()->word() . $this->faker->numberBetween(10, 99),
            'description' => $this->faker->paragraph(),
            'badge_text' => $this->faker->optional()->word(),
            'badge_color' => $this->faker->hexColor(),
            'is_featured' => $this->faker->boolean(20),
            'display_order' => $this->faker->numberBetween(0, 100),
            'discount_type' => $this->faker->randomElement(['percentage', 'fixed']),
            'discount_value' => $this->faker->randomFloat(2, 5, 50),
            'code' => $this->faker->optional()->word() . $this->faker->numberBetween(10, 99),
            'starts_at' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'ends_at' => $this->faker->optional()->dateTimeBetween('+1 month', '+3 months'),
            'is_active' => $this->faker->boolean(80),
            'usage_limit' => $this->faker->optional()->numberBetween(10, 1000),
            'used_count' => $this->faker->numberBetween(0, 100),
        ];
    }
} 