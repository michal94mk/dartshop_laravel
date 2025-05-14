<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class AdminController extends BaseAdminController
{
    public function index() {
        return view('admin.index');
    }
} 