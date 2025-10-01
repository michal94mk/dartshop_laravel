<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusChangedMail;

/**
 * Service for handling admin order business logic (listing, filtering, CRUD, status update, deletion)
 */
class OrderAdminService
{
    /**
     * Get paginated, filtered, and sorted orders.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getOrdersWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::with(['user']);

        // Search
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
                if (strtolower($search) === 'gość' || strtolower($search) === 'gosc') {
                    $q->orWhereNull('user_id');
                }
                if (filter_var($search, FILTER_VALIDATE_EMAIL)) {
                    $emailParts = explode('@', $search);
                    $username = $emailParts[0];
                    $q->orWhere('email', '=', $search)
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$username}%");
                    $q->orWhereHas('user', function($uq) use ($search) {
                        $uq->where('email', '=', $search);
                    });
                } else if (strpos($search, '@') !== false) {
                    $emailParts = explode('@', $search);
                    $username = $emailParts[0];
                    $q->orWhere('email', '=', $search)
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$username}%");
                    $q->orWhereHas('user', function($uq) use ($search) {
                        $uq->where('email', 'like', "%{$search}%");
                    });
                } else {
                    $q->orWhere('email', 'like', "%{$search}%");
                }
                if (!filter_var($search, FILTER_VALIDATE_EMAIL) && strpos($search, '@') === false && strtolower($search) !== 'gość' && strtolower($search) !== 'gosc') {
                    $q->orWhereHas('user', function($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            });
        }

        // Status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Sorting
        $sortField = $filters['sort_field'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get a single order by ID (with user and items).
     *
     * @param int $id
     * @return Order
     */
    public function getById(int $id): Order
    {
        return Order::with(['user', 'items.product'])->findOrFail($id);
    }

    /**
     * Create a new order.
     *
     * @param array $data
     * @param array $items
     * @return Order
     */
    public function create(array $data, array $items): Order
    {
        DB::beginTransaction();
        try {
            $orderNumber = $data['order_number'] ?? Order::generateOrderNumber();
            $data['order_number'] = $orderNumber;
            $data['subtotal'] = $data['subtotal'] ?? ($data['total'] - $data['shipping_cost'] + ($data['discount'] ?? 0));
            $order = new Order();
            $order->fill($data);
            $order->save();
            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'] ?? null,
                    'quantity' => $item['quantity'],
                    'product_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);
            }
            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an order and its items.
     *
     * @param int $id
     * @param array $data
     * @param array|null $items
     * @return Order
     */
    public function update(int $id, array $data, ?array $items = null): Order
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);
            
            // Prevent changing user_id for existing orders
            if (isset($data['user_id']) && $order->user_id && $order->user_id != $data['user_id']) {
                throw new \Exception('Nie można zmienić użytkownika zamówienia po jego utworzeniu.');
            }
            
            // Block editing for completed, shipped, or delivered orders
            if (in_array($order->status, [OrderStatus::Shipped, OrderStatus::Delivered])) {
                throw new \Exception('Nie można edytować zamówienia po jego realizacji (wysłane lub dostarczone).');
            }
            
            // Prevent editing user data for existing orders (security measure)
            $userDataFields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'postal_code', 'country'];
            foreach ($userDataFields as $field) {
                if (isset($data[$field]) && $order->$field !== $data[$field]) {
                    throw new \Exception('Nie można edytować danych użytkownika w istniejącym zamówieniu. Zamówienie to dokument prawny.');
                }
            }
            $order->update($data);
            if ($items !== null) {
                $order->items()->delete();
                foreach ($items as $item) {
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'] ?? null,
                        'quantity' => $item['quantity'],
                        'product_price' => $item['price'],
                        'total_price' => $item['price'] * $item['quantity'],
                    ]);
                }
                if (isset($data['shipping_cost'])) {
                    $subtotal = $order->items()->sum('total_price');
                    $shipping_cost = $data['shipping_cost'];
                    $discount = $data['discount'] ?? 0;
                    $order->update([
                        'subtotal' => $subtotal,
                        'shipping_cost' => $shipping_cost,
                        'discount' => $discount,
                        'total' => max(0, $subtotal + $shipping_cost - $discount),
                    ]);
                }
            }
            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update order status and optionally notify customer.
     *
     * @param int $id
     * @param string $status
     * @param bool $notifyCustomer
     * @param string|null $note
     * @return Order
     */
    public function updateStatus(int $id, string $status, bool $notifyCustomer = false, ?string $note = null): Order
    {
        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $order->status = $status;
        $order->save();
        if ($notifyCustomer) {
            Mail::to($order->email)->queue(new OrderStatusChangedMail(
                $order,
                $oldStatus,
                $status,
                $note
            ));
        }
        return $order;
    }

    /**
     * Delete an order and its items (only if status is pending).
     *
     * @param int $id
     * @return void
     * @throws \Exception
     */
    public function deleteById(int $id): void
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);
            
            // Only allow deletion of pending orders
            if ($order->status !== OrderStatus::Pending) {
                throw new \Exception('Można usuwać tylko zamówienia ze statusem "Oczekujące".');
            }
            
            $order->items()->delete();
            $order->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
} 