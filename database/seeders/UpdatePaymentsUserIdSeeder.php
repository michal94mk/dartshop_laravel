<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Order;

class UpdatePaymentsUserIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aktualizuj wszystkie płatności, przypisując im user_id na podstawie zamówienia
        $payments = Payment::whereNull('user_id')->get();
        
        foreach ($payments as $payment) {
            $order = Order::find($payment->order_id);
            if ($order && $order->user_id) {
                $payment->update(['user_id' => $order->user_id]);
            }
        }
        
        $this->command->info('Zaktualizowano ' . $payments->count() . ' płatności.');
    }
}
