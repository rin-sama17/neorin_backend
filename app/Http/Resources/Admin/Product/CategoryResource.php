<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'status' => $this->status,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            "attributes" => $this->allAttributes(),
            'category_attribute_with_values' => $this->allAttributes()->map(function ($attribute) {
    return [
        'id' => $attribute->id,
        'name' => $attribute->name,
        'unit' => $attribute->unit,
        'type' => $attribute->type,
        'values' => $attribute->categoryValues->map(fn ($value) => [
            'id' => $value->id,
            'value' => $value->value,
        ]),
    ];
}),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }


    public function with($request)
    {
        return [
            'statue' => true,
        ];
    }
}
