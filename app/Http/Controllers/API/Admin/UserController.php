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
            $query = User::query();
            
            // Apply filters
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            if ($request->has('role')) {
                if ($request->role === 'admin') {
                    $query->where('is_admin', true);
                } else if ($request->role === 'user') {
                    $query->where('is_admin', false);
                }
            }
            
            if ($request->has('verified')) {
                if ($request->verified == 1) {
                    $query->whereNotNull('email_verified_at');
                } else if ($request->verified == 0) {
                    $query->whereNull('email_verified_at');
                }
            }
            
            // Apply sorting
            $sortField = $request->sort_field ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            $query->orderBy($sortField, $sortDirection);
            
            // Paginate results
            $perPage = $this->getPerPage($request);
            $users = $query->paginate($perPage);
            
            return response()->json($users);
        } catch (\Exception $e) {
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
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|in:admin,user',
                'email_verified' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => $request->role === 'admin',
                'email_verified_at' => $request->email_verified ? now() : null,
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

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($id),
                ],
                'password' => 'nullable|string|min:8',
                'role' => 'required|in:admin,user',
                'email_verified' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'is_admin' => $request->role === 'admin',
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            // Update email verification status
            if ($request->has('email_verified')) {
                $userData['email_verified_at'] = $request->email_verified ? now() : null;
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
            $user = User::findOrFail($id);
            
            // Prevent deleting the last admin user
            if ($user->is_admin) {
                $adminCount = User::where('is_admin', true)->count();
                if ($adminCount <= 1) {
                    return $this->errorResponse('Cannot delete the last admin user', 403);
                }
            }
            
            $user->delete();

            return $this->successResponse('User deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting user: ' . $e->getMessage(), 500);
        }
    }
} 