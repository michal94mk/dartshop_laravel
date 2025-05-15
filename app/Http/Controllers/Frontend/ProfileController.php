<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ShippingAddressRequest;
use App\Models\Product;
use App\Models\ShippingAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    
    /**
     * Display the user's orders.
     */
    public function orders(Request $request): View
    {
        $orders = $request->user()->orders()->latest()->paginate(10);
        
        return view('profile.orders', [
            'orders' => $orders,
        ]);
    }
    
    /**
     * Display the user's payments.
     */
    public function payments(Request $request): View
    {
        $payments = $request->user()->payments()->latest()->paginate(10);
        
        return view('profile.payments', [
            'payments' => $payments,
        ]);
    }
    
    /**
     * Display the user's favorite products.
     */
    public function favorites(Request $request): View
    {
        $favoriteProducts = $request->user()->favoriteProducts()->paginate(12);
        
        return view('profile.favorites', [
            'products' => $favoriteProducts,
        ]);
    }
    
    /**
     * Toggle product in user's favorites.
     */
    public function toggleFavorite(Request $request, Product $product)
    {
        $user = $request->user();
        $isAdded = false;
        
        if ($user->favoriteProducts()->where('product_id', $product->id)->exists()) {
            $user->favoriteProducts()->detach($product->id);
            $message = 'Produkt został usunięty z ulubionych.';
            $status = 'removed';
        } else {
            $user->favoriteProducts()->attach($product->id);
            $message = 'Produkt został dodany do ulubionych.';
            $status = 'added';
            $isAdded = true;
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $status
            ]);
        }
        
        return back()->with('status', $message);
    }
    
    /**
     * Display the user's shipping addresses.
     */
    public function addresses(Request $request): View
    {
        $addresses = $request->user()->shippingAddresses()->get();
        
        return view('profile.addresses', [
            'addresses' => $addresses,
        ]);
    }
    
    /**
     * Show form for creating a new shipping address.
     */
    public function createAddress(): View
    {
        return view('profile.address-form', [
            'address' => new ShippingAddress(),
        ]);
    }
    
    /**
     * Store a new shipping address.
     */
    public function storeAddress(ShippingAddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // If this is the first address or is_default is checked, set as default
        $isDefault = $request->boolean('is_default') || $user->shippingAddresses()->count() === 0;
        
        // If setting this address as default, unset any existing default
        if ($isDefault) {
            $user->shippingAddresses()->update(['is_default' => false]);
        }
        
        $user->shippingAddresses()->create(array_merge(
            $request->validated(),
            ['is_default' => $isDefault]
        ));
        
        return Redirect::route('profile.addresses')->with('status', 'address-added');
    }
    
    /**
     * Show form for editing a shipping address.
     */
    public function editAddress(ShippingAddress $address): View
    {
        $this->authorize('update', $address);
        
        return view('profile.address-form', [
            'address' => $address,
        ]);
    }
    
    /**
     * Update a shipping address.
     */
    public function updateAddress(ShippingAddressRequest $request, ShippingAddress $address): RedirectResponse
    {
        $this->authorize('update', $address);
        
        $user = $request->user();
        
        // If setting this address as default, unset any existing default
        if ($request->boolean('is_default')) {
            $user->shippingAddresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $address->is_default = true;
        }
        
        $address->fill($request->validated());
        $address->save();
        
        return Redirect::route('profile.addresses')->with('status', 'address-updated');
    }
    
    /**
     * Delete a shipping address.
     */
    public function destroyAddress(ShippingAddress $address): RedirectResponse
    {
        $this->authorize('delete', $address);
        
        $address->delete();
        
        return Redirect::route('profile.addresses')->with('status', 'address-deleted');
    }
} 