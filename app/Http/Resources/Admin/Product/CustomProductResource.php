<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'status'      => $this->status,
            'items'       => $this->whenLoaded('items', function () {
                return $this->items->map(fn($item) => [
                    'id'          => $item->id,
                    'name'        => $item->name,
                    'category_id' => $item->category_id,
                    'category'    => $item->category ? [
                        'id'   => $item->category->id,
                        'name' => $item->category->name,
                    ] : null,
                    'is_required' => $item->is_required,
                    'min_qty'     => $item->min_qty,
                    'max_qty'     => $item->max_qty,
                    'sort'        => $item->sort,
                    'status'      => $item->status,
                ]);
            }),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
