<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutPageController extends BaseAdminController
{
    /**
     * Display the first about page data.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get the first about page record or create a new one if none exists
        $aboutPage = AboutPage::first();
        
        if (!$aboutPage) {
            $aboutPage = new AboutPage();
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
        $aboutPages = AboutPage::orderBy('display_order')->get();
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
        $aboutPage = AboutPage::findOrFail($id);
        return response()->json($aboutPage);
    }

    /**
     * Create a new about page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image_path' => 'nullable|string',
            'layout_type' => 'nullable|string|in:standard,full-width,text-only,image-only',
            'image_position' => 'nullable|string|in:left,right,top,bottom,background',
            'background_color' => 'nullable|string',
            'is_hero_section' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $aboutPage = new AboutPage();
        $aboutPage->fill($request->only([
            'title',
            'content',
            'meta_title',
            'meta_description',
            'display_order',
            'is_active',
            'image_path',
            'layout_type',
            'image_position',
            'background_color',
            'is_hero_section'
        ]));
        
        $aboutPage->save();
        
        return response()->json($aboutPage);
    }

    /**
     * Update the first about page data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image_path' => 'nullable|string',
            'layout_type' => 'nullable|string|in:standard,full-width,text-only,image-only',
            'image_position' => 'nullable|string|in:left,right,top,bottom,background',
            'background_color' => 'nullable|string',
            'is_hero_section' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Get the first about page record or create a new one if none exists
        $aboutPage = AboutPage::first();
        
        if (!$aboutPage) {
            $aboutPage = new AboutPage();
        }
        
        // Update the about page data with only the fields that are in the fillable array
        $aboutPage->fill($request->only([
            'title',
            'content',
            'meta_title',
            'meta_description',
            'display_order',
            'is_active',
            'image_path',
            'layout_type',
            'image_position',
            'background_color',
            'is_hero_section'
        ]));
        
        $aboutPage->save();
        
        return response()->json($aboutPage);
    }
    
    /**
     * Update the specified about page by ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image_path' => 'nullable|string',
            'layout_type' => 'nullable|string|in:standard,full-width,text-only,image-only',
            'image_position' => 'nullable|string|in:left,right,top,bottom,background',
            'background_color' => 'nullable|string',
            'is_hero_section' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $aboutPage = AboutPage::findOrFail($id);
        
        // Update the about page data with only the fields that are in the fillable array
        $aboutPage->fill($request->only([
            'title',
            'content',
            'meta_title',
            'meta_description',
            'display_order',
            'is_active',
            'image_path',
            'layout_type',
            'image_position',
            'background_color',
            'is_hero_section'
        ]));
        
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
        $aboutPage = AboutPage::findOrFail($id);
        $aboutPage->delete();
        
        return response()->json(['message' => 'Strona usunięta pomyślnie']);
    }
} 