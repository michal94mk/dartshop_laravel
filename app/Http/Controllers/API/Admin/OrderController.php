<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseAdminController
{
    /**
     * Display a listing of the orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Order::with(['user']);
            
            // Apply filters
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhereHas('user', function($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      })
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            // Apply sorting
            $sortField = $request->sort_field ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            $query->orderBy($sortField, $sortDirection);
            
            // Paginate results
            $perPage = $this->getPerPage($request);
            $orders = $query->paginate($perPage);
            
            return response()->json($orders);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching orders: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'nullable|exists:users,id',
                'name' => 'required_without:user_id|string|max:255',
                'email' => 'required_without:user_id|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'postal_code' => 'required|string|max:20',
                'country' => 'required|string|max:100',
                'total' => 'required|numeric|min:0',
                'status' => 'required|in:pending,processing,completed,cancelled',
                'payment_method' => 'required|string|max:100',
                'payment_status' => 'required|in:pending,completed,failed',
                'shipping_method' => 'required|string|max:100',
                'shipping_cost' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Start transaction
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'total' => $request->total,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'shipping_method' => $request->shipping_method,
                'shipping_cost' => $request->shipping_cost,
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            DB::commit();

            return $this->successResponse('Order created successfully', $order, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error creating order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $order = Order::with(['user', 'items.product'])->findOrFail($id);
            return response()->json($order);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching order: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Update the specified order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,processing,completed,cancelled',
                'payment_status' => 'required|in:pending,completed,failed',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $order->update([
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'notes' => $request->notes,
            ]);

            return $this->successResponse('Order updated successfully', $order);
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update order status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,processing,completed,cancelled',
                'note' => 'nullable|string',
                'notify_customer' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $order->status = $request->status;
            $order->save();

            // Add status history entry
            $order->statusHistory()->create([
                'status' => $request->status,
                'note' => $request->note,
                'user_id' => $request->user()->id,
            ]);

            // Send email notification if requested
            if ($request->notify_customer) {
                // Send email (to be implemented)
            }

            return $this->successResponse('Order status updated successfully', $order);
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating order status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified order from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            // Start transaction
            DB::beginTransaction();
            
            // Delete order items
            $order->items()->delete();
            
            // Delete order
            $order->delete();
            
            DB::commit();

            return $this->successResponse('Order deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error deleting order: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Generate invoice for an order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        try {
            $order = Order::with(['user', 'items.product'])->findOrFail($id);
            
            // Generate PDF invoice (to be implemented)
            // This is a placeholder that would normally generate a PDF
            $invoiceData = [
                'order' => $order,
                'company' => [
                    'name' => 'Dartshop',
                    'address' => '123 Main St, City',
                    'postal_code' => '12-345',
                    'city' => 'City',
                    'country' => 'Country',
                    'tax_id' => '1234567890',
                ],
                'invoice_number' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'invoice_date' => now()->format('Y-m-d'),
            ];
            
            // For now, return JSON for testing
            return response()->json($invoiceData);
            
            // In production, this would return a PDF:
            // return PDF::loadView('invoices.template', $invoiceData)
            //    ->stream('invoice-' . $order->id . '.pdf');
        } catch (\Exception $e) {
            return $this->errorResponse('Error generating invoice: ' . $e->getMessage(), 500);
        }
    }
} 