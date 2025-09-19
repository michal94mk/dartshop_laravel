<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

class TestOrderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order-email {email} {--order-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test order confirmation email by sending to specified email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $orderId = $this->option('order-id');

        if ($orderId) {
            // Use existing order
            $order = Order::with('items')->find($orderId);
            if (!$order) {
                $this->error("Order with ID {$orderId} not found.");
                return 1;
            }
        } else {
            // Get latest order or create dummy data
            $order = Order::with('items')->latest()->first();
            if (!$order) {
                $this->error("No orders found in database. Please create an order first.");
                return 1;
            }
        }

        $this->info("Sending order confirmation email for order {$order->order_number} to {$email}...");

        try {
            Mail::to($email)->send(new OrderConfirmationMail($order));
            $this->info("Email sent successfully!");
            
            $this->info("Order details:");
            $this->line("- Order Number: {$order->order_number}");
            $this->line("- Customer: {$order->first_name} {$order->last_name}");
            $this->line("- Email: {$order->email}");
            $this->line("- Total: {$order->total} PLN");
            $this->line("- Items: {$order->items->count()}");
            
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
