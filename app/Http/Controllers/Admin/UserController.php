<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends BaseAdminController
{
    public function index()
    {
        $perPage = $this->getPerPage();
        $users = User::paginate($perPage);
        
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.users.index-tailwind', compact('users'));
        }
        
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.users.form-tailwind', compact('user'));
        }
        
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->only(['name', 'email', 'role']));
        
        return redirect()->route('admin.users.index', $request->has('tailwind') ? ['tailwind' => 1] : [])
            ->with('success', 'User updated successfully');
    }

    public function destroy(Request $request, User $user)
    {
        $user->delete();
        
        return redirect()->route('admin.users.index', $request->has('tailwind') ? ['tailwind' => 1] : [])
            ->with('success', 'User deleted successfully');
    }
} 