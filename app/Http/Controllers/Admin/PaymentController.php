<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Enums\PaymentStatus;

/**
 * Payment management controller for the admin panel.
 * 
 * This controller handles all payment-related operations in the admin area,
 * including listing, viewing, and updating payment records.
 */
class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     * 
     * Retrieves all payments with optional filtering by status and search terms.
     * Payments are paginated and displayed in the admin panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Payment::with('order');

        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhere('transaction_id', 'like', "%{$search}%");
        }

        $payments = $query->latest()->paginate(10);
        $statuses = PaymentStatus::cases();

        return view('admin.payments.index', compact('payments', 'statuses'));
    }

    /**
     * Display the specified payment details.
     * 
     * Shows detailed information about a specific payment record,
     * including associated order information if available.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\View\View
     */
    public function show(Payment $payment)
    {
        $payment->load('order');
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     * 
     * Presents a form allowing administrators to update the payment status
     * and add notes to payment records.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\View\View
     */
    public function edit(Payment $payment)
    {
        $payment->load('order');
        $statuses = PaymentStatus::cases();
        return view('admin.payments.edit', compact('payment', 'statuses'));
    }

    /**
     * Update the specified payment in storage.
     * 
     * Processes the form submission to update payment status and notes.
     * If payment status is updated to COMPLETED, also updates the associated 
     * order status to PROCESSING.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_column(PaymentStatus::cases(), 'value')),
            'notes' => 'nullable|string|max:1000',
        ]);

        $payment->update($validated);

        // Update order status if payment is completed
        if ($payment->status === PaymentStatus::COMPLETED && $payment->order) {
            $payment->order->update(['status' => \App\Enums\OrderStatus::PROCESSING]);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment has been updated successfully.');
    }
} 