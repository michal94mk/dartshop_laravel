<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutPageController extends BaseAdminController
{
    /**
     * Display the about page data.
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
     * Update the about page data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
            'mission' => 'nullable|string',
            'vision' => 'nullable|string',
            'values' => 'nullable|string',
            'team' => 'nullable|array',
            'team.*.name' => 'required|string|max:255',
            'team.*.position' => 'required|string|max:255',
            'team.*.bio' => 'nullable|string',
            'team.*.photo_url' => 'nullable|url',
            'history' => 'nullable|array',
            'history.*.year' => 'required|integer',
            'history.*.title' => 'required|string|max:255',
            'history.*.description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Get the first about page record or create a new one if none exists
        $aboutPage = AboutPage::first();
        
        if (!$aboutPage) {
            $aboutPage = new AboutPage();
        }
        
        // Update the about page data
        $aboutPage->fill($request->all());
        $aboutPage->save();
        
        return response()->json($aboutPage);
    }
} 