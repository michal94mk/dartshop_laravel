<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends BaseAdminController
{
    /**
     * Display a listing of the users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Log request and auth info
            \Illuminate\Support\Facades\Log::info('UserController@index called', [
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'request' => $request->all()
            ]);
            
            $query = User::query();
            
            // Log initial query count
            \Illuminate\Support\Facades\Log::info('Initial query count: ' . $query->count());
            
            // Apply filters
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
                \Illuminate\Support\Facades\Log::info('After search filter count: ' . $query->count());
            }
            
            if ($request->has('role') && !empty($request->role)) {
                if ($request->role === 'admin') {
                    $query->where('is_admin', true);
                } else if ($request->role === 'user') {
                    $query->where('is_admin', false);
                }
                \Illuminate\Support\Facades\Log::info('After role filter count: ' . $query->count());
            }
            
            if ($request->has('verified') && $request->verified !== null) {
                if ($request->verified == 1) {
                    $query->whereNotNull('email_verified_at');
                } else if ($request->verified == 0) {
                    $query->whereNull('email_verified_at');
                }
                \Illuminate\Support\Facades\Log::info('After verified filter count: ' . $query->count());
            }
            
            if ($request->has('account_type') && !empty($request->account_type)) {
                if ($request->account_type === 'google') {
                    $query->whereNotNull('google_id');
                } else if ($request->account_type === 'local') {
                    $query->whereNull('google_id');
                }
                \Illuminate\Support\Facades\Log::info('After account_type filter count: ' . $query->count());
            }
            
            // Apply sorting
            $sortField = $request->sort_field ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            $query->orderBy($sortField, $sortDirection);
            
            // Get SQL for debugging
            \Illuminate\Support\Facades\Log::info('Final SQL', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
            
            // Paginate results
            $perPage = $this->getPerPage($request);
            $users = $query->paginate($perPage);
            
            \Illuminate\Support\Facades\Log::info('Final results', [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage()
            ]);
            
            return response()->json($users);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in UserController@index: ' . $e->getMessage());
            return $this->errorResponse('Error fetching users: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|in:admin,user',
                'verified' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => $request->role === 'admin',
                'email_verified_at' => $request->verified ? now() : null,
            ]);

            return $this->successResponse('User created successfully', $user, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error creating user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = User::with('orders')->findOrFail($id);
            return response()->json($user);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching user: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Build validation rules based on user type
            $rules = [
                'name' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'password' => 'nullable|string|min:8',
                'role' => 'required|in:admin,user',
                'verified' => 'boolean',
            ];
            
            // Only require email validation for non-Google OAuth users
            if (empty($user->google_id)) {
                $rules['email'] = [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($id),
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $userData = [
                'name' => $request->name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'is_admin' => $request->role === 'admin',
            ];
            
            // Only allow email changes for non-Google OAuth users
            if (empty($user->google_id) && $request->has('email')) {
                $userData['email'] = $request->email;
            }

            // Only update password if provided and user is not Google OAuth user
            if ($request->filled('password') && empty($user->google_id)) {
                $userData['password'] = Hash::make($request->password);
            }

            // Update email verification status (only for non-Google OAuth users)
            if ($request->has('verified') && empty($user->google_id)) {
                $userData['email_verified_at'] = $request->verified ? now() : null;
            }

            $user->update($userData);

            return $this->successResponse('User updated successfully', $user);
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            \Illuminate\Support\Facades\Log::info('UserController@destroy called', [
                'user_id' => $id,
                'admin_user_id' => \Illuminate\Support\Facades\Auth::id()
            ]);
            
            $user = User::findOrFail($id);
            
            \Illuminate\Support\Facades\Log::info('User found for deletion', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'is_google_user' => !empty($user->google_id)
            ]);
            
            // Prevent deleting the current authenticated user
            if ($user->id == \Illuminate\Support\Facades\Auth::id()) {
                \Illuminate\Support\Facades\Log::warning('Attempt to delete current user', [
                    'user_id' => $user->id,
                    'auth_user_id' => \Illuminate\Support\Facades\Auth::id()
                ]);
                return $this->errorResponse('You cannot delete yourself', 403);
            }
            
            // Prevent deleting the last admin user
            if ($user->is_admin) {
                $adminCount = User::where('is_admin', true)->count();
                \Illuminate\Support\Facades\Log::info('Admin count check', [
                    'user_id' => $user->id,
                    'admin_count' => $adminCount
                ]);
                if ($adminCount <= 1) {
                    return $this->errorResponse('Cannot delete the last admin user', 403);
                }
            }
            
            // Check for existing relations that might prevent deletion
            $relations = [];
            if ($user->orders()->count() > 0) {
                $relations['orders'] = $user->orders()->count();
            }
            if ($user->reviews()->count() > 0) {
                $relations['reviews'] = $user->reviews()->count();
            }
            if ($user->cartItems()->count() > 0) {
                $relations['cart_items'] = $user->cartItems()->count();
            }
            if ($user->payments()->count() > 0) {
                $relations['payments'] = $user->payments()->count();
            }
            if ($user->favoriteProducts()->count() > 0) {
                $relations['favorite_products'] = $user->favoriteProducts()->count();
            }
            if ($user->shippingAddresses()->count() > 0) {
                $relations['shipping_addresses'] = $user->shippingAddresses()->count();
            }
            
            if (!empty($relations)) {
                \Illuminate\Support\Facades\Log::warning('User has existing relations', [
                    'user_id' => $user->id,
                    'relations' => $relations
                ]);
                
                $relationsList = [];
                foreach ($relations as $type => $count) {
                    $relationsList[] = ucfirst(str_replace('_', ' ', $type)) . ": $count";
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete user with existing data',
                    'relations' => $relations,
                    'relations_text' => implode(', ', $relationsList),
                    'user_info' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ], 422);
            }
            
            \Illuminate\Support\Facades\Log::info('Attempting to delete user', [
                'user_id' => $user->id
            ]);
            
            $result = $user->delete();
            
            \Illuminate\Support\Facades\Log::info('User deletion result', [
                'user_id' => $id,
                'delete_result' => $result
            ]);

            return $this->successResponse('User deleted successfully');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in UserController@destroy', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Error deleting user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify the specified user's email.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify($id)
    {
        try {
            \Illuminate\Support\Facades\Log::info('UserController@verify called', [
                'user_id' => $id,
                'admin_user_id' => \Illuminate\Support\Facades\Auth::id()
            ]);
            
            $user = User::findOrFail($id);
            
            \Illuminate\Support\Facades\Log::info('User found for verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'current_verified_at' => $user->email_verified_at,
                'is_google_user' => !empty($user->google_id)
            ]);
            
            if (!empty($user->google_id)) {
                \Illuminate\Support\Facades\Log::warning('Cannot verify Google OAuth user', [
                    'user_id' => $user->id,
                    'google_id' => $user->google_id
                ]);
                return $this->errorResponse('Google OAuth users are automatically verified and cannot be manually verified', 400);
            }
            
            if ($user->email_verified_at) {
                \Illuminate\Support\Facades\Log::warning('User email is already verified', [
                    'user_id' => $user->id,
                    'verified_at' => $user->email_verified_at
                ]);
                return $this->errorResponse('User email is already verified', 400);
            }
            
            $result = $user->update(['email_verified_at' => now()]);
            
            \Illuminate\Support\Facades\Log::info('User verification update result', [
                'user_id' => $user->id,
                'update_result' => $result,
                'new_verified_at' => $user->fresh()->email_verified_at
            ]);
            
            // Refresh user from database to get latest data
            $user = $user->fresh();

            return $this->successResponse('User email verified successfully', $user);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in UserController@verify', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Error verifying user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Force delete user with all related data.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDestroy($id)
    {
        try {
            \Illuminate\Support\Facades\Log::info('UserController@forceDestroy called', [
                'user_id' => $id,
                'admin_user_id' => \Illuminate\Support\Facades\Auth::id()
            ]);
            
            $user = User::findOrFail($id);
            
            // Prevent deleting the current authenticated user
            if ($user->id == \Illuminate\Support\Facades\Auth::id()) {
                return $this->errorResponse('You cannot delete yourself', 403);
            }
            
            // Prevent deleting the last admin user
            if ($user->is_admin) {
                $adminCount = User::where('is_admin', true)->count();
                if ($adminCount <= 1) {
                    return $this->errorResponse('Cannot delete the last admin user', 403);
                }
            }
            
            \Illuminate\Support\Facades\Log::info('Force deleting user with all relations', [
                'user_id' => $user->id
            ]);
            
            // Delete all related data in proper order
            $user->shippingAddresses()->delete();
            $user->favoriteProducts()->detach();
            $user->cartItems()->delete();
            $user->reviews()->delete();
            $user->payments()->delete();
            
            // For orders, we might want to keep them but anonymize the user
            $user->orders()->update(['user_id' => null]);
            
            // Finally delete the user
            $result = $user->delete();
            
            \Illuminate\Support\Facades\Log::info('User force deletion completed', [
                'user_id' => $id,
                'delete_result' => $result
            ]);

            return $this->successResponse('User and all related data deleted successfully');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in UserController@forceDestroy', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Error force deleting user: ' . $e->getMessage(), 500);
        }
    }
} 