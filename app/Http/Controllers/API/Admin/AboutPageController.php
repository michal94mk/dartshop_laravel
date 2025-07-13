<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
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
            'header_style' => 'nullable|string',
            'header_margin' => 'nullable|string',
            'content_layout' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $aboutPage = new AboutUs();
        $aboutPage->fill($request->only([
            'title',
            'content',
            'meta_title',
            'meta_description',
            'header_style',
            'header_margin',
            'content_layout'
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
            'header_style' => 'nullable|string',
            'header_margin' => 'nullable|string',
            'content_layout' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Get the first about page record or create a new one if none exists
        $aboutPage = AboutUs::first();
        
        if (!$aboutPage) {
            $aboutPage = new AboutUs();
        }
        
        // Update the about page data with only the fields that are in the fillable array
        $aboutPage->fill($request->only([
            'title',
            'content',
            'meta_title',
            'meta_description',
            'header_style',
            'header_margin',
            'content_layout'
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
            'header_style' => 'nullable|string',
            'header_margin' => 'nullable|string',
            'content_layout' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $aboutPage = AboutUs::findOrFail($id);
        
        // Update the about page data with only the fields that are in the fillable array
        $aboutPage->fill($request->only([
            'title',
            'content',
            'meta_title',
            'meta_description',
            'header_style',
            'header_margin',
            'content_layout'
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
        $aboutPage = AboutUs::findOrFail($id);
        $aboutPage->delete();
        
        return response()->json(['message' => 'Strona usunięta pomyślnie']);
    }
    

} 