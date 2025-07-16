<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Enums\OrderStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . $this->faker->unique()->numberBetween(1000, 9999),
            'status' => $this->faker->randomElement([
                OrderStatus::Pending->value,
                OrderStatus::Processing->value,
                OrderStatus::Shipped->value,
                OrderStatus::Delivered->value,
                OrderStatus::Cancelled->value,
                OrderStatus::Refunded->value
            ]),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'notes' => $this->faker->optional()->sentence(),
            'subtotal' => $this->faker->randomFloat(2, 10, 1000),
            'shipping_cost' => $this->faker->randomFloat(2, 0, 50),
            'discount' => $this->faker->randomFloat(2, 0, 100),
            'total' => $this->faker->randomFloat(2, 10, 1000),
            'payment_method' => $this->faker->randomElement(['stripe', 'paypal', 'bank_transfer']),
            'shipping_method' => $this->faker->randomElement(['standard', 'express']),
            'payment_status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
        ];
    }
} 