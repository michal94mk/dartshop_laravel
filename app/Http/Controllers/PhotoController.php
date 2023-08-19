<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            $image->move(public_path('images'), $imageName);
            return 'images/' . $imageName;
        }

        return null;
    }

    public function destroy($imagePath)
    {
        if ($imagePath) {
            $fullImagePath = public_path($imagePath);
            if (file_exists($fullImagePath)) {
                unlink($fullImagePath);
            }
        }

        return response()->json(['message' => 'Image deleted'], 200);
    }
}
