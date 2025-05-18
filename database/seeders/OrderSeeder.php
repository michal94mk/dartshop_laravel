<?php

namespace Database\Seeders;

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
        $paymentStatuses = [
            PaymentStatus::PENDING->value, 
            PaymentStatus::COMPLETED->value, 
            PaymentStatus::FAILED->value, 
            PaymentStatus::REFUNDED->value
        ];
        $shippingStatuses = ['pending', 'shipped', 'delivered'];
        $paymentMethods = ['credit_card', 'paypal', 'bank_transfer', 'blik'];
        
        // Create 20-30 orders
        $orderCount = rand(20, 30);
        
        for ($i = 0; $i < $orderCount; $i++) {
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $shippingStatus = $shippingStatuses[array_rand($shippingStatuses)];
            
            // Create date in the past 6 months
            $orderDate = $faker->dateTimeBetween('-6 months', 'now');
            
            // Calculate totals
            $totalAmount = 0;
            $taxAmount = 0;
            $shippingAmount = rand(0, 20);
            $discountAmount = rand(0, 50);
            
            // Create shipping and billing addresses
            $shippingAddress = $faker->name . "\n" . 
                              $faker->streetAddress . "\n" . 
                              $faker->postcode . ' ' . $faker->city . "\n" . 
                              'Poland';
                              
            $billingAddress = rand(0, 5) > 0 ? $shippingAddress : 
                             $faker->name . "\n" . 
                             $faker->streetAddress . "\n" . 
                             $faker->postcode . ' ' . $faker->city . "\n" . 
                             'Poland';
            
            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . Carbon::parse($orderDate)->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'total_amount' => 0, // Will update after adding items
                'tax_amount' => 0, // Will update after adding items
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'shipping_status' => $shippingStatus,
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
                'notes' => rand(0, 1) ? $faker->sentence : null,
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
                
                $totalAmount += $total;
            }
            
            // Calculate tax (23% VAT)
            $taxAmount = round($totalAmount * 0.23, 2);
            
            // Update order with correct totals
            $order->update([
                'total_amount' => $totalAmount + $taxAmount + $shippingAmount - $discountAmount,
                'tax_amount' => $taxAmount
            ]);
            
            // Create payment for the order
            if ($paymentStatus !== PaymentStatus::PENDING->value) {
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $paymentMethod,
                    'transaction_id' => strtoupper(Str::random(10)),
                    'amount' => $order->total_amount,
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
