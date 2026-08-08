<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryAttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'unit'           => $this->unit,
            'category'       => $this->whenLoaded('category'),
            'values'         => $this->whenLoaded('categoryValues'),
            'type'           => $this->type,
            'selection_type' => $this->selection_type,
            'is_required'    => $this->is_required,
            'min_value'      => $this->min_value,
            'max_value'      => $this->max_value,
            'default_value'  => $this->default_value,
            'sort'           => $this->sort,
            'is_filterable'  => $this->is_filterable,
            'is_searchable'  => $this->is_searchable,
            'status'         => $this->status,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }

    public function with($request)
    {
        return [
            'statue' => true,
        ];
    }
}
