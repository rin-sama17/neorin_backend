<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Http\Resources\Admin\Content\PageCollection;
use App\Http\Resources\Admin\Content\PageResource;
use App\Models\Content\Page;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class PageController extends Controller
{
use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return new PageCollection(Page::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageRequest $request)
    {
        $input = $request->all();
        $page = Page::create($input);
        return new PageResource($page);
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {
    return new PageResource($page);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
      $input = $request->all();
        $page->update($input);
        return new PageResource($page);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();
        return $this->success(null,"صفحه با موفقیت حذف شد");
    }
}
