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
     * Upload an image for tutorials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadTutorialImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $image = $request->file('image');
            $fileName = 'tutorial_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // Store the image in the public storage
            $path = $image->storeAs('images/tutorials', $fileName, 'public');
            
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
