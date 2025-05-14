<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends BaseAdminController
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage();
        $query = User::query()->with('roles');
        
        // Wyszukiwanie przez metodę z BaseAdminController
        $this->applySearch($query, $request, ['name', 'email']);
        
        $users = $query->paginate($perPage);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        // Update user details with validated data
        $user->update($request->safe()->only(['name', 'email']));
        
        // Update user role if provided
        if ($request->has('role')) {
            $user->syncRoles([$request->role]);
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        // Prevent deleting the admin user or currently logged in user
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete user with admin role.');
        }
        
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete your own account.');
        }

        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
} 