<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImageUploadController extends BaseAdminController
{
    /**
     * Upload an image for the About page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadAboutImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $image = $request->file('image');
            $fileName = 'about_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // Store the image in the public storage
            $path = $image->storeAs('images/about', $fileName, 'public');
            
            // Generate the public URL
            $url = asset('storage/' . $path);
            
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }
}
