<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Category;

class CategoriesComposer
{
    public function compose(View $view)
    {
        $categories = Category::paginate(10);
        $view->with('categories', $categories);
    }
}
