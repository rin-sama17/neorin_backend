<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomProductItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'custom_product_id' => $this->custom_product_id,
            'category_id' => $this->category_id,
            'calculation_profile_id' => $this->calculation_profile_id,
            'category'    => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ]),
            'calculation_profile' => $this->whenLoaded('calculationProfile', fn() => [
                'id'            => $this->calculationProfile->id,
                'name'          => $this->calculationProfile->name,
                'strategy_type' => $this->calculationProfile->strategy_type,
            ]),
            'name'        => $this->name,
            'is_required' => $this->is_required,
            'min_qty'     => $this->min_qty,
            'max_qty'     => $this->max_qty,
            'sort'        => $this->sort,
            'status'      => $this->status,
            'attributes'  => $this->when($this->category, function () {
                return $this->category->customProductAttributes();
            }),
            'fabrics'     => $this->when($this->category, function () {
                return $this->category->fabric()->where('status', 1)->get()->map(fn($f) => [
                    'id'       => $f->id,
                    'title'    => $f->title,
                    'slug'     => $f->slug,
                    'material' => $f->material,
                    'width'    => $f->width,
                    'price'    => $f->price,
                    'image'    => $f->image,
                ]);
            }),
            'rules'       => $this->whenLoaded('rules', function () {
                return CustomProductRuleResource::collection($this->rules);
            }),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
