<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

/**
 * Service class for admin user management.
 * Handles listing, filtering, creating, updating, and deleting users.
 */
class UserAdminService
{
    /**
     * Get paginated users with filters and sorting.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getUsersWithFilters(Request $request): LengthAwarePaginator
    {
        $query = User::query();

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && !empty($request->role)) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } else if ($request->role === 'user') {
                $query->where('is_admin', false);
            }
        }

        if ($request->has('verified') && $request->verified !== null) {
            if ($request->verified == 1) {
                $query->where(function($q) {
                    $q->whereNotNull('email_verified_at')
                      ->orWhereNotNull('google_id');
                });
            } else if ($request->verified == 0) {
                $query->whereNull('email_verified_at')
                      ->whereNull('google_id');
            }
        }

        if ($request->has('account_type') && !empty($request->account_type)) {
            if ($request->account_type === 'google') {
                $query->whereNotNull('google_id');
            } else if ($request->account_type === 'local') {
                $query->whereNull('google_id');
            }
        }

        // Apply sorting
        $sortField = $request->sort_field ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $result = $query->paginate($perPage);
        Log::info('UserAdminService paginator result', ['result' => $result->toArray()]);
        return $result;
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        // ... create user logic (to be implemented)
        return User::create($data);
    }

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        // ... update user logic (to be implemented)
        $user->update($data);
        return $user;
    }

    /**
     * Delete a user (soft delete).
     *
     * @param User $user
     * @return void
     */
    public function deleteUser(User $user): void
    {
        $user->delete();
    }

    /**
     * Force delete a user (permanent delete).
     *
     * @param User $user
     * @return void
     */
    public function forceDeleteUser(User $user): void
    {
        $user->forceDelete();
    }

    /**
     * Mark a user as verified.
     *
     * @param User $user
     * @return void
     */
    public function verifyUser(User $user): void
    {
        $user->email_verified_at = now();
        $user->save();
    }

    /**
     * Get a user by ID with orders relation and default shipping address.
     *
     * @param int $id
     * @return User|null
     */
    public function getUserWithOrders(int $id): ?User
    {
        $user = User::with(['orders', 'shippingAddresses' => function($query) {
            $query->where('is_default', true)->first();
        }])->find($id);
        
        if ($user) {
            // Get default shipping address
            $defaultAddress = $user->shippingAddresses()->where('is_default', true)->first();
            
            // Add address data to user object for easy access
            if ($defaultAddress) {
                $user->address = $defaultAddress->address_line_1;
                $user->city = $defaultAddress->city;
                $user->postal_code = $defaultAddress->postal_code;
                $user->phone = $defaultAddress->phone;
            }
        }
        
        return $user;
    }

    /**
     * Find a user by ID or fail.
     *
     * @param int $id
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findUserOrFail(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Find a user by ID (with trashed) or fail.
     *
     * @param int $id
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findUserWithTrashedOrFail(int $id): User
    {
        // withTrashed() will work only if User model uses SoftDeletes
        // If not, fallback to normal findOrFail
        if (method_exists(User::class, 'withTrashed')) {
            return User::withTrashed()->findOrFail($id);
        }
        return User::findOrFail($id);
    }
} 