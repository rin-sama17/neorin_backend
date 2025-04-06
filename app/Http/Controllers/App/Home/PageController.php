<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\PageCollection;
use App\Models\Content\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
   public function index(){
        return new PageCollection(Page::all()->whereNull('parent_id'));
    }
}
