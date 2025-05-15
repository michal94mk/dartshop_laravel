<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\PaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Frontend PaymentController handles all payment-related actions for customers.
 * 
 * This controller manages the complete payment flow including displaying
 * the payment form, processing payment completion and cancellation,
 * and handling payment gateway webhook notifications.
 */
class PaymentController extends Controller
{
    /**
     * Process payment for an order by showing the payment interface.
     * 
     * Presents the appropriate payment form based on the selected payment method
     * during checkout. Verifies ownership of the order before proceeding.
     *
     * @param Order $order The order to process payment for
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
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
                ->with('info', 'This order has already been paid for.');
        }
        
        // This is a simplified example. In a real application, you would integrate with a payment gateway
        // such as PayU, Przelewy24, or Stripe.
        
        return view('frontend.payments.process', compact('order'));
    }
    
    /**
     * Handle successful payment completion.
     * 
     * Creates or updates the payment record and marks it as completed.
     * Updates the associated order status to processing. In a production
     * environment, this would verify the payment with the payment gateway.
     *
     * @param PaymentRequest $request The incoming request
     * @param Order $order The order being paid for
     * @return \Illuminate\Http\RedirectResponse
     */
    public function complete(PaymentRequest $request, Order $order)
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
                ->with('success', 'Payment completed successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment processing error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->back()
                ->with('error', 'An error occurred while processing your payment. Please try again later.');
        }
    }
    
    /**
     * Handle payment cancellation by the customer.
     * 
     * Creates or updates the payment record with a failed status when
     * a customer cancels the payment process. Redirects to the order
     * detail page with appropriate messaging.
     *
     * @param Request $request The incoming request
     * @param Order $order The order for which payment is being cancelled
     * @return \Illuminate\Http\RedirectResponse
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
            ->with('info', 'Payment has been cancelled.');
    }
    
    /**
     * Process webhook notifications from payment gateway.
     * 
     * Handles asynchronous notifications from payment providers regarding
     * payment status changes. In a production environment, this would
     * verify the webhook signature and update orders accordingly.
     *
     * @param Request $request The incoming webhook request
     * @return \Illuminate\Http\JsonResponse
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
