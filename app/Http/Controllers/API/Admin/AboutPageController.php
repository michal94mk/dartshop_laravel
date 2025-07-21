<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\AboutUs;
use App\Http\Requests\Admin\AboutPageRequest;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Admin/About Page",
 *     description="API Endpoints for admin about page management"
 * )
 */

class AboutPageController extends BaseAdminController
{
    /**
     * Display the first about page data.
     *
     * @OA\Get(
     *     path="/api/admin/about-page",
     *     summary="Get about page data (admin)",
     *     description="Get the first about page record or create default if none exists",
     *     tags={"Admin/About Page"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AboutUs")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required"
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Get the first about page record or create a new one if none exists
            $aboutPage = AboutUs::first();
            
            if (!$aboutPage) {
                $aboutPage = new AboutUs();
                $aboutPage->title = 'O nas';
                $aboutPage->content = 'Dodaj treść strony O nas.';
                $aboutPage->save();
            }
            
            return response()->json($aboutPage);
        } catch (\Exception $e) {
            Log::error('Failed to get about page data', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Error fetching about page data: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Display all about pages.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        try {
            $aboutPages = AboutUs::orderBy('created_at')->get();
            return response()->json($aboutPages);
        } catch (\Exception $e) {
            Log::error('Failed to get all about pages', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Error fetching about pages: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Display the specified about page.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $aboutPage = AboutUs::findOrFail($id);
            return response()->json($aboutPage);
        } catch (\Exception $e) {
            Log::error('Failed to get about page', [
                'error' => $e->getMessage(),
                'id' => $id,
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('About page not found', 404);
        }
    }

    /**
     * Create a new about page.
     *
     * @param  \App\Http\Requests\Admin\AboutPageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function create(AboutPageRequest $request)
    {
        try {
            $aboutPage = new AboutUs();
            $aboutPage->fill($request->validated());
            
            $aboutPage->save();
            
            return response()->json($aboutPage);
        } catch (\Exception $e) {
            Log::error('Failed to create about page', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Error creating about page: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the first about page.
     *
     * @param  \App\Http\Requests\Admin\AboutPageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(AboutPageRequest $request)
    {
        try {
            $aboutPage = AboutUs::first();
            
            if (!$aboutPage) {
                $aboutPage = new AboutUs();
            }
            
            $aboutPage->fill($request->validated());
            $aboutPage->save();
            
            return response()->json($aboutPage);
        } catch (\Exception $e) {
            Log::error('Failed to update about page', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Error updating about page: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified about page.
     *
     * @param  \App\Http\Requests\Admin\AboutPageRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateById(AboutPageRequest $request, $id)
    {
        try {
            $aboutPage = AboutUs::findOrFail($id);
            $aboutPage->fill($request->validated());
            $aboutPage->save();
            
            return response()->json($aboutPage);
        } catch (\Exception $e) {
            Log::error('Failed to update about page by ID', [
                'error' => $e->getMessage(),
                'id' => $id,
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Error updating about page: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified about page from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $aboutPage = AboutUs::findOrFail($id);
            $aboutPage->delete();
            
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Failed to delete about page', [
                'error' => $e->getMessage(),
                'id' => $id,
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Error deleting about page: ' . $e->getMessage(), 500);
        }
    }
} 