<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Admin\OrderRequest;

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
     * @param  \App\Http\Requests\Admin\OrderRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderRequest $request)
    {
        try {
            // Log entire request for debugging
            Log::info('Order creation request data:', $request->all());
            
            // Start transaction
            DB::beginTransaction();

            // Generate a unique order number
            $orderNumber = $request->order_number ?? Order::generateOrderNumber();
            Log::info('Generated order number: ' . $orderNumber);

            // Build the order data with explicit attributes
            $orderData = $request->validated();
            $orderData['order_number'] = $orderNumber;
            $orderData['subtotal'] = $request->input('subtotal', ($request->input('total') - $request->input('shipping_cost')));
            
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
            $id = (int) $id;
            $order = Order::with(['user', 'items.product'])->findOrFail($id);
            return response()->json($order);
        } catch (ModelNotFoundException $e) {
            Log::error('Order not found for show:', ['id' => $id, 'error' => $e->getMessage()]);
            return $this->errorResponse('Zamówienie o ID ' . $id . ' nie zostało znalezione', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching order: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Update the specified order in storage.
     *
     * @param  \App\Http\Requests\Admin\OrderRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OrderRequest $request, $id)
    {
        try {
            // Ensure ID is an integer
            $id = (int) $id;
            Log::info('Order update called with ID:', ['id' => $id, 'type' => gettype($id)]);
            
            $order = Order::findOrFail($id);

            Log::info('Order update request data:', $request->all());
            
            // Start transaction
            DB::beginTransaction();
            
            // Update order data
            $orderData = $request->validated();
            
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Order not found for update:', ['id' => $id, 'error' => $e->getMessage()]);
            return $this->errorResponse('Zamówienie o ID ' . $id . ' nie zostało znalezione', 404);
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
            $id = (int) $id;
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
        } catch (ModelNotFoundException $e) {
            Log::error('Order not found for status update:', ['id' => $id, 'error' => $e->getMessage()]);
            return $this->errorResponse('Zamówienie o ID ' . $id . ' nie zostało znalezione', 404);
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
            $id = (int) $id;
            $order = Order::findOrFail($id);
            
            // Start transaction
            DB::beginTransaction();
            
            // Delete order items
            $order->items()->delete();
            
            // Delete order
            $order->delete();
            
            DB::commit();

            return $this->successResponse('Order deleted successfully');
        } catch (ModelNotFoundException $e) {
            Log::error('Order not found for delete:', ['id' => $id, 'error' => $e->getMessage()]);
            return $this->errorResponse('Zamówienie o ID ' . $id . ' nie zostało znalezione', 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error deleting order: ' . $e->getMessage(), 500);
        }
    }
    

} 