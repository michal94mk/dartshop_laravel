<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'payment_method' => $this->faker->randomElement(['stripe', 'paypal', 'bank_transfer']),
            'payment_status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'PLN',
            'transaction_id' => $this->faker->optional()->regexify('[A-Za-z0-9]{20}'),
            'payment_intent_id' => $this->faker->optional()->regexify('pi_[A-Za-z0-9]{24}'),
            'payment_data' => json_encode([
                'customer_email' => $this->faker->email(),
                'customer_name' => $this->faker->name(),
            ]),
        ];
    }

    /**
     * Indicate that the payment is successful.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'completed',
        ]);
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Indicate that the payment failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'failed',
        ]);
    }
} 