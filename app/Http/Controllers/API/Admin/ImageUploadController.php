<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Admin/Image Upload",
 *     description="API Endpoints for admin image upload management"
 * )
 */

class ImageUploadController extends BaseAdminController
{


    /**
     * Upload an image for tutorials.
     *
     * @OA\Post(
     *     path="/api/admin/upload/tutorial-image",
     *     summary="Upload tutorial image",
     *     description="Upload an image for tutorials with validation",
     *     tags={"Admin/Image Upload"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file (jpeg, png, jpg, gif, webp, max 5MB)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="path", type="string", example="images/tutorials/tutorial_1234567890_abc123.jpg"),
     *             @OA\Property(property="url", type="string", example="http://example.com/storage/images/tutorials/tutorial_1234567890_abc123.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error uploading image")
     *         )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadTutorialImage(TutorialImageUploadRequest $request)
    {
        try {
            $image = $request->file('image');
            $fileName = 'tutorial_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // Store the image in the public storage
            $path = $image->storeAs('images/tutorials', $fileName, 'public');
            
            // Generate the public URL
            $url = asset('storage/' . $path);
            
            return $this->successResponse('Obrazek został przesłany', [
                'path' => $path,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas przesyłania obrazka: ' . $e->getMessage(), 500);
        }
    }
}
