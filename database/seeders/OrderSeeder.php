<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pl_PL');
        
        // Get users and products
        $users = User::where('is_admin', false)->get();
        $products = Product::all();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->error('Users or products not found. Run UserSeeder and ProductSeeder first.');
            return;
        }
        
        // Order statuses
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentMethods = ['credit_card', 'paypal', 'bank_transfer', 'blik'];
        
        // Create 20-30 orders
        $orderCount = rand(20, 30);
        
        for ($i = 0; $i < $orderCount; $i++) {
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];
            
            // Create date in the past 6 months
            $orderDate = $faker->dateTimeBetween('-6 months', 'now');
            
            // Calculate totals
            $subtotal = 0;
            $shippingCost = rand(0, 20);
            $discount = rand(0, 50);
            
            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . Carbon::parse($orderDate)->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'status' => $status,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'postal_code' => $faker->postcode,
                'country' => 'Polska',
                'notes' => rand(0, 1) ? $faker->sentence : null,
                'subtotal' => 0, // Will update after adding items
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => 0, // Will update after adding items
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'session_id' => Str::random(32),
                'promotion_code' => rand(0, 1) ? strtoupper(Str::random(8)) : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate
            ]);
            
            // Add 1-5 products to the order
            $itemCount = rand(1, 5);
            $orderProducts = $products->random($itemCount);
            
            foreach ($orderProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                $total = $price * $quantity;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $total,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate
                ]);
                
                $subtotal += $total;
            }
            
            // Update order with correct totals
            $total = $subtotal + $shippingCost - $discount;
            $order->update([
                'subtotal' => $subtotal,
                'total' => $total
            ]);
            
            // Create payment for the order
            if ($status !== 'pending') {
                $paymentStatus = ($status == 'completed') ? PaymentStatus::COMPLETED->value : 
                                 ($status == 'cancelled' ? PaymentStatus::FAILED->value : PaymentStatus::PENDING->value);
                
                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'payment_method' => $order->payment_method,
                    'transaction_id' => strtoupper(Str::random(10)),
                    'amount' => $total,
                    'status' => $paymentStatus,
                    'notes' => rand(0, 1) ? $faker->sentence : null,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate
                ]);
            }
        }
        
        $this->command->info('Orders created successfully!');
    }
}
