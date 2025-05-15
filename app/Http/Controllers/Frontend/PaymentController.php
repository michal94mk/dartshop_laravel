<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Process payment for an order.
     */
    public function process(Order $order)
    {
        // Verify that the order belongs to the current user or session
        if (Auth::check() && $order->user_id !== Auth::id() && $order->session_id !== session()->getId()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if order has already been paid
        if ($order->payment && $order->payment->status === PaymentStatus::COMPLETED) {
            return redirect()->route('order.confirmation', ['order' => $order->id])
                ->with('info', 'To zamówienie zostało już opłacone.');
        }
        
        // This is a simplified example. In a real application, you would integrate with a payment gateway
        // such as PayU, Przelewy24, or Stripe.
        
        return view('frontend.payments.process', compact('order'));
    }
    
    /**
     * Handle payment completion.
     */
    public function complete(Request $request, Order $order)
    {
        // Verify that the order belongs to the current user or session
        if (Auth::check() && $order->user_id !== Auth::id() && $order->session_id !== session()->getId()) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            DB::beginTransaction();
            
            // In a real application, you would verify the payment with the payment gateway
            // before marking it as completed
            
            // Create or update payment record
            $payment = Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'user_id' => Auth::id(),
                    'payment_method' => $order->payment_method,
                    'amount' => $order->total,
                    'currency' => 'PLN',
                    'status' => PaymentStatus::COMPLETED,
                    'transaction_data' => [
                        'transaction_id' => 'test_' . uniqid(),
                        'payment_date' => now()->toDateTimeString(),
                    ],
                ]
            );
            
            // Update order status
            $order->update([
                'status' => OrderStatus::PROCESSING,
            ]);
            
            DB::commit();
            
            return redirect()->route('order.confirmation', ['order' => $order->id])
                ->with('success', 'Płatność została zakończona pomyślnie!');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment processing error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas przetwarzania płatności. Spróbuj ponownie później.');
        }
    }
    
    /**
     * Handle payment cancellation.
     */
    public function cancel(Request $request, Order $order)
    {
        // Verify that the order belongs to the current user or session
        if (Auth::check() && $order->user_id !== Auth::id() && $order->session_id !== session()->getId()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Create payment record with failed status
        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => Auth::id(),
                'payment_method' => $order->payment_method,
                'amount' => $order->total,
                'currency' => 'PLN',
                'status' => PaymentStatus::FAILED,
                'transaction_data' => [
                    'cancelled_at' => now()->toDateTimeString(),
                    'reason' => 'User cancelled the payment',
                ],
            ]
        );
        
        return redirect()->route('order.show', ['order' => $order->id])
            ->with('info', 'Płatność została anulowana.');
    }
    
    /**
     * Process webhook notifications from payment gateway.
     */
    public function webhook(Request $request)
    {
        // This is a simplified example. In a real application, you would:
        // 1. Verify the webhook signature
        // 2. Parse the webhook data
        // 3. Update the payment and order status accordingly
        
        Log::info('Payment webhook received', $request->all());
        
        return response()->json(['status' => 'success']);
    }
}
