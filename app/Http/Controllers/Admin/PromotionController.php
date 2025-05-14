<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class PromotionController extends BaseAdminController
{
    /**
     * Display a listing of promotions.
     */
    public function index()
    {
        $perPage = $this->getPerPage();
        // Admin view of all promotions
        return view('admin.promotions.index');
    }

    /**
     * Show the form for creating a new promotion.
     */
    public function create()
    {
        return view('admin.promotions.create');
    }

    /**
     * Store a newly created promotion in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified promotion.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified promotion.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified promotion in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified promotion from storage.
     */
    public function destroy(string $id)
    {
        //
    }
} 