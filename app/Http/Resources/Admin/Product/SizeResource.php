<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SizeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $discount = $this->product?->getActiveDiscount();

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'width'          => $this->width,
            'height'         => $this->height,
            'stock'          => $this->stock,
            'image'          => $this->image,
            'original_price' => $this->price,
            'discount'       => $discount ? [
                'value'       => $discount->value,
                'final_price' => $discount->calculateFinalPrice($this->price),
            ] : null,
            'deleted_at'     => $this->deleted_at,
      ];
    }
}
