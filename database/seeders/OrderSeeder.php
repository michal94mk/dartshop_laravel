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
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data instead of faker
        $firstNames = ['Jan', 'Anna', 'Piotr', 'Maria', 'Tomasz', 'Katarzyna', 'Michał', 'Joanna'];
        $lastNames = ['Kowalski', 'Nowak', 'Wiśniewski', 'Wójcik', 'Kowalczyk', 'Kamińska', 'Lewandowski', 'Zielińska'];
        $emails = ['jan@email.com', 'anna@email.com', 'piotr@email.com', 'maria@email.com', 'tomasz@email.com', 'katarzyna@email.com', 'michal@email.com', 'joanna@email.com'];
        $phones = ['123-456-789', '987-654-321', '555-123-456', '111-222-333', '444-555-666', '777-888-999', '123-987-456', '654-321-987'];
        $addresses = ['ul. Główna 1', 'ul. Polna 5', 'ul. Słoneczna 10', 'ul. Krótka 3', 'ul. Długa 7', 'ul. Nowa 2', 'ul. Stara 8', 'ul. Miła 4'];
        $cities = ['Warszawa', 'Kraków', 'Gdańsk', 'Wrocław', 'Poznań', 'Łódź', 'Katowice', 'Lublin'];
        $postalCodes = ['00-001', '30-001', '80-001', '50-001', '60-001', '90-001', '40-001', '20-001'];
        
        // Get users and products
        $users = User::where('is_admin', false)->get();
        $products = Product::all();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->error('Users or products not found. Run UserSeeder and ProductSeeder first.');
            return;
        }
        
        // Order statuses
        $statuses = [
            OrderStatus::Pending->value,
            OrderStatus::Processing->value,
            OrderStatus::Delivered->value,
            OrderStatus::Cancelled->value
        ];
        $paymentMethods = ['credit_card', 'paypal', 'bank_transfer', 'blik'];
        
        // Create 20-30 orders
        $orderCount = rand(20, 30);
        
        for ($i = 0; $i < $orderCount; $i++) {
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];
            
            // Create date in the past 6 months
            $orderDate = now()->subDays(rand(1, 180));
            
            // Calculate totals
            $subtotal = 0;
            $shippingCost = rand(0, 20);
            $discount = rand(0, 50);
            
            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . $orderDate->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'status' => $status,
                'first_name' => $firstNames[array_rand($firstNames)],
                'last_name' => $lastNames[array_rand($lastNames)],
                'email' => $emails[array_rand($emails)],
                'phone' => $phones[array_rand($phones)],
                'address' => $addresses[array_rand($addresses)],
                'city' => $cities[array_rand($cities)],
                'postal_code' => $postalCodes[array_rand($postalCodes)],
                'notes' => rand(0, 1) ? 'Dodatkowe uwagi do zamówienia' : null,
                'subtotal' => 0, // Will update after adding items
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => 0, // Will update after adding items
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_intent_id' => $status !== OrderStatus::Pending->value ? 'pi_' . Str::random(24) : null,
                'stripe_session_id' => $status !== OrderStatus::Pending->value ? 'cs_' . Str::random(24) : null,
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
                    'product_price' => $price,
                    'total_price' => $total,
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
            if ($status !== OrderStatus::Pending->value) {
                $paymentStatus = ($status == OrderStatus::Delivered->value) ? PaymentStatus::Completed->value : 
                                ($status == OrderStatus::Cancelled->value ? PaymentStatus::Failed->value : PaymentStatus::Processing->value);
                
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $order->payment_method,
                    'payment_status' => $paymentStatus,
                    'transaction_id' => strtoupper(Str::random(10)),
                    'amount' => $total,
                    'currency' => 'PLN',
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate
                ]);
            }
        }
        
        $this->command->info('Orders created successfully!');
    }
}
