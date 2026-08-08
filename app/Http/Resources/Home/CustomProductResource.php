<?php

namespace App\Http\Resources\Home;

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
            'items'       => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    $category = $item->category;

                    return [
                        'id'          => $item->id,
                        'name'        => $item->name,
                        'is_required' => $item->is_required,
                        'min_qty'     => $item->min_qty,
                        'max_qty'     => $item->max_qty,
                        'sort'        => $item->sort,
                        'category'    => $category ? [
                            'id'   => $category->id,
                            'name' => $category->name,
                        ] : null,
                        'calculation_profile' => $item->calculationProfile ? [
                            'id'            => $item->calculationProfile->id,
                            'name'          => $item->calculationProfile->name,
                            'strategy_type' => $item->calculationProfile->strategy_type,
                        ] : null,
                        'attributes'  => $category
                            ? $category->customProductAttributes()
                            : [],
                        'fabrics'     => $category
                            ? $category->fabric()->where('status', 1)->get()->map(fn($f) => [
                                'id'       => $f->id,
                                'title'    => $f->title,
                                'slug'     => $f->slug,
                                'material' => $f->material,
                                'width'    => $f->width,
                                'price'    => $f->price,
                                'image'    => $f->image,
                                'colors'   => $f->colors,
                            ])
                            : [],
                        'rules_count' => $item->rules_count ?? $item->rules()->count(),
                    ];
                });
            }),
            'created_at'  => $this->created_at,
        ];
    }
}
