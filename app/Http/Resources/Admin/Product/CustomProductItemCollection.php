<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomProductItemCollection extends ResourceCollection
{
    public $collects = CustomProductItemResource::class;

    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
    }
}
