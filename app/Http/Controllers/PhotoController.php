<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $path = $image->storeAs('public/images', $imageName);

            $imageUrl = Storage::url($path);

            return $imageUrl;
        }

        return null;
    }

    public function destroy($imagePath)
    {
        if ($imagePath) {
            $path = str_replace('storage/', 'public/', $imagePath);

            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        return response()->json(['message' => 'Image deleted'], 200);
    }
}
