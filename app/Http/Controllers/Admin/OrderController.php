<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $query = Order::query();
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by customer name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%");
            });
        }
        
        // Order by
        $query->orderBy('created_at', 'desc');
        
        $orders = $query->paginate(20);
        
        $statuses = OrderStatus::cases();
        
        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $statuses = OrderStatus::cases();
        $paymentStatuses = PaymentStatus::cases();
        
        return view('admin.orders.edit', compact('order', 'statuses', 'paymentStatuses'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            $order->update([
                'status' => $request->status,
                'notes' => $request->notes,
            ]);
            
            // If payment status is also updated
            if ($request->has('payment_status') && $order->payment) {
                $order->payment->update([
                    'status' => $request->payment_status,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Zamówienie zostało zaktualizowane.');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas aktualizacji zamówienia: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show invoice for the order.
     */
    public function invoice(Order $order)
    {
        // Generate invoice for the order
        // This is a simplified example. In a real application, you would:
        // 1. Generate a PDF invoice
        // 2. Return it for display or download
        
        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Export orders to CSV/Excel.
     */
    public function export(Request $request)
    {
        // Export orders based on filters
        // This is a placeholder. In a real application, you would:
        // 1. Generate a CSV/Excel file with the filtered orders
        // 2. Return it for download
        
        return redirect()->back()->with('info', 'Funkcja eksportu będzie dostępna wkrótce.');
    }
}
