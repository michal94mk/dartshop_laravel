<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            Log::info('OrderController@index called with filters:', $request->all());
            
            $query = Order::with(['user']);
            
            // Apply filters
            if ($request->has('search') && !empty($request->search)) {
                $search = trim($request->search);
                
                // Log search term for debugging
                Log::info('Searching orders with term: ' . $search);
                
                $query->where(function($q) use ($search) {
                    // Basic fields search
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('order_number', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%");
                    
                    // Special case: search for "Gość" (guest orders)
                    if (strtolower($search) === 'gość' || strtolower($search) === 'gosc') {
                        $q->orWhereNull('user_id');
                    }
                    
                    // Email search - both exact and partial matching
                    if (filter_var($search, FILTER_VALIDATE_EMAIL)) {
                        // For valid emails, try exact match, partial match, and username part
                        $emailParts = explode('@', $search);
                        $username = $emailParts[0];
                        
                        $q->orWhere('email', '=', $search)
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$username}%");
                          
                        // Also search in user email
                        $q->orWhereHas('user', function($uq) use ($search) {
                            $uq->where('email', '=', $search);
                        });
                    } else if (strpos($search, '@') !== false) {
                        // Contains @ but not valid email - try exact, partial, and username part
                        $emailParts = explode('@', $search);
                        $username = $emailParts[0];
                        
                        $q->orWhere('email', '=', $search)
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$username}%");
                          
                        // Also search in user email
                        $q->orWhereHas('user', function($uq) use ($search) {
                            $uq->where('email', 'like', "%{$search}%");
                        });
                    } else {
                        // Partial email search (e.g., searching for "gmail" or "admin")
                        $q->orWhere('email', 'like', "%{$search}%");
                    }
                    
                    // User name search (only if not searching for email or guest)
                    if (!filter_var($search, FILTER_VALIDATE_EMAIL) && 
                        strpos($search, '@') === false && 
                        strtolower($search) !== 'gość' && 
                        strtolower($search) !== 'gosc') {
                        $q->orWhereHas('user', function($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                    }
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
            
            // Log query for debugging
            Log::info('Orders query SQL:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
            
            // Paginate results
            $perPage = $this->getPerPage($request);
            $orders = $query->paginate($perPage);
            
            Log::info('OrderController@index success. Orders count: ' . $orders->count());
            
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
            // Log entire request for debugging
            Log::info('Order creation request data:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'user_id' => 'nullable|exists:users,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'postal_code' => 'required|string|max:20',
                'country' => 'required|string|max:100',
                'total' => 'required|numeric|min:0',
                'status' => 'required|in:pending,processing,completed,shipped,delivered,cancelled,refunded',
                'payment_status' => 'required|in:pending,completed,failed',
                'payment_method' => 'required|string|max:100',
                'shipping_method' => 'required|string|max:100',
                'shipping_cost' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'order_number' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                Log::warning('Order validation failed:', ['errors' => $validator->errors()->toArray()]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Start transaction
            DB::beginTransaction();

            // Generate a unique order number
            $orderNumber = $request->order_number ?? Order::generateOrderNumber();
            Log::info('Generated order number: ' . $orderNumber);

            // Build the order data with explicit attributes
            $orderData = [
                'order_number' => $orderNumber,
                'user_id' => $request->input('user_id'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'postal_code' => $request->input('postal_code'),
                'country' => $request->input('country', 'Polska'),
                'total' => $request->input('total'),
                'status' => $request->input('status', 'pending'),
                'payment_status' => $request->input('payment_status', 'pending'),
                'payment_method' => $request->input('payment_method'),
                'shipping_method' => $request->input('shipping_method'),
                'shipping_cost' => $request->input('shipping_cost'),
                'notes' => $request->input('notes'),
                'subtotal' => $request->input('subtotal', ($request->input('total') - $request->input('shipping_cost'))),
            ];
            
            // Log orderData for debugging
            Log::info('Order data being created:', $orderData);

            // Create order explicitly with all fields
            $order = new Order();
            $order->fill($orderData);
            $order->save();
            
            Log::info('Order created with ID: ' . $order->id);

            // Create order items
            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => isset($item['product_name']) ? $item['product_name'] : null,
                    'quantity' => $item['quantity'],
                    'product_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);
            }

            DB::commit();
            Log::info('Order creation successful. Transaction committed.');

            return $this->successResponse('Order created successfully', $order, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
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

            Log::info('Order update request data:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'user_id' => 'nullable|exists:users,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'postal_code' => 'required|string|max:20',
                'country' => 'required|string|max:100',
                'status' => 'required|in:pending,processing,completed,shipped,delivered,cancelled,refunded',
                'payment_status' => 'required|in:pending,completed,failed',
                'payment_method' => 'required|string|max:100',
                'shipping_method' => 'required|string|max:100',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                Log::warning('Order update validation failed:', ['errors' => $validator->errors()->toArray()]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Start transaction
            DB::beginTransaction();
            
            // Update order data
            $orderData = [
                'user_id' => $request->input('user_id'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'postal_code' => $request->input('postal_code'),
                'country' => $request->input('country', 'Polska'),
                'status' => $request->input('status'),
                'payment_status' => $request->input('payment_status'),
                'payment_method' => $request->input('payment_method'),
                'shipping_method' => $request->input('shipping_method'),
                'notes' => $request->input('notes'),
            ];
            
            // Log orderData for debugging
            Log::info('Order data being updated:', $orderData);

            // Update order
            $order->update($orderData);
            
            // Update order items if provided
            if ($request->has('items') && is_array($request->items)) {
                // Delete existing items
                $order->items()->delete();
                
                // Create new items
                foreach ($request->items as $item) {
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'product_name' => isset($item['product_name']) ? $item['product_name'] : null,
                        'quantity' => $item['quantity'],
                        'product_price' => $item['price'],
                        'total_price' => $item['price'] * $item['quantity'],
                    ]);
                }
                
                // Recalculate order total
                if ($request->has('shipping_cost')) {
                    $subtotal = $order->items()->sum('total_price');
                    $shipping_cost = $request->input('shipping_cost');
                    
                    $order->update([
                        'subtotal' => $subtotal,
                        'shipping_cost' => $shipping_cost,
                        'total' => $subtotal + $shipping_cost,
                    ]);
                }
            }

            DB::commit();
            Log::info('Order update successful. Transaction committed.');

            return $this->successResponse('Order updated successfully', $order);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
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
                'status' => 'required|in:pending,processing,completed,shipped,delivered,cancelled,refunded',
                'note' => 'nullable|string',
                'notify_customer' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $oldStatus = $order->status;
            $order->status = $request->status;
            $order->save();

            // Send email notification if requested
            if ($request->notify_customer) {
                Mail::to($order->email)->queue(new \App\Mail\OrderStatusChangedMail(
                    $order, 
                    $oldStatus, 
                    $request->status, 
                    $request->note
                ));
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
    

} 