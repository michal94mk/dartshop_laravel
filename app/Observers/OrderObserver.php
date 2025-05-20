<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "creating" event.
     * This runs BEFORE saving to the database
     */
    public function creating(Order $order): void
    {
        // Make sure order_number is set
        if (empty($order->order_number)) {
            $order->order_number = Order::generateOrderNumber();
            Log::warning('OrderObserver: order_number was empty, generated a new one', [
                'order_number' => $order->order_number
            ]);
        }

        Log::info('OrderObserver: Order creating event', [
            'order_data' => $order->toArray()
        ]);
    }

    /**
     * Handle the Order "created" event.
     * This runs AFTER saving to the database
     */
    public function created(Order $order): void
    {
        Log::info('OrderObserver: Order created successfully', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);
    }

    /**
     * Handle the Order "saving" event.
     */
    public function saving(Order $order): void
    {
        Log::info('OrderObserver: Order saving event', [
            'attributes' => $order->getDirty(),
            'order_number' => $order->order_number
        ]);
    }

    /**
     * Handle the Order "saved" event.
     */
    public function saved(Order $order): void
    {
        Log::info('OrderObserver: Order saved event', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
