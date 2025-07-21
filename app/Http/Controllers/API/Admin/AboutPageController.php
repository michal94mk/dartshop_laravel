<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\AboutPageRequest;

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
        // Get the first about page record or create a new one if none exists
        $aboutPage = AboutUs::first();
        
        if (!$aboutPage) {
            $aboutPage = new AboutUs();
            $aboutPage->title = 'O nas';
            $aboutPage->content = 'Dodaj treść strony O nas.';
            $aboutPage->save();
        }
        
        return response()->json($aboutPage);
    }
    
    /**
     * Display all about pages.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $aboutPages = AboutUs::orderBy('created_at')->get();
        return response()->json($aboutPages);
    }
    
    /**
     * Display the specified about page.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $aboutPage = AboutUs::findOrFail($id);
        return response()->json($aboutPage);
    }

    /**
     * Create a new about page.
     *
     * @param  \App\Http\Requests\Admin\AboutPageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function create(AboutPageRequest $request)
    {
        $aboutPage = new AboutUs();
        $aboutPage->fill($request->validated());
        
        $aboutPage->save();
        
        return response()->json($aboutPage);
    }

    /**
     * Update the first about page data.
     *
     * @param  \App\Http\Requests\Admin\AboutPageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(AboutPageRequest $request)
    {
        // Get the first about page record or create a new one if none exists
        $aboutPage = AboutUs::first();
        
        if (!$aboutPage) {
            $aboutPage = new AboutUs();
        }
        
        // Update the about page data with only the fields that are in the fillable array
        $aboutPage->fill($request->validated());
        
        $aboutPage->save();
        
        return response()->json($aboutPage);
    }
    
    /**
     * Update the specified about page by ID.
     *
     * @param  \App\Http\Requests\Admin\AboutPageRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateById(AboutPageRequest $request, $id)
    {
        $aboutPage = AboutUs::findOrFail($id);
        
        // Update the about page data with only the fields that are in the fillable array
        $aboutPage->fill($request->validated());
        
        $aboutPage->save();
        
        return response()->json($aboutPage);
    }
    
    /**
     * Remove the specified about page.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $aboutPage = AboutUs::findOrFail($id);
        $aboutPage->delete();
        
        return response()->json(['message' => 'Strona usunięta pomyślnie']);
    }
    

} 