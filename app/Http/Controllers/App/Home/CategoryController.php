<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\CategoryCollection;
use App\Models\Product\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return new CategoryCollection(Category::all()->whereNull('parent_id'));
    }
    public function showAll()
    {
        return new CategoryCollection(Category::all());
    }
}
