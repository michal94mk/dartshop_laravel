<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Enums\PaymentStatus;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with('order');

        // Filtrowanie po statusie
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Wyszukiwanie
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
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load('order');
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        $payment->load('order');
        $statuses = PaymentStatus::cases();
        return view('admin.payments.edit', compact('payment', 'statuses'));
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_column(PaymentStatus::cases(), 'value')),
            'notes' => 'nullable|string|max:1000',
        ]);

        $payment->update($validated);

        // Aktualizacja statusu zamówienia jeśli płatność została zakończona
        if ($payment->status === PaymentStatus::COMPLETED && $payment->order) {
            $payment->order->update(['status' => \App\Enums\OrderStatus::PROCESSING]);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Płatność została zaktualizowana.');
    }
} 