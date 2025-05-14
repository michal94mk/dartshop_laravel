<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends BaseAdminController
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $perPage = $this->getPerPage();
        $users = User::with('roles')->paginate($perPage);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->only(['name', 'email']));
        
        // Update user role
        $role = $request->input('role');
        if ($role) {
            $user->syncRoles([$role]);
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
} 