<?php

namespace App\Http\Controllers\Admin;

class AdminController extends BaseAdminController
{
    public function index() {
        return view('admin.index');
    }
} 