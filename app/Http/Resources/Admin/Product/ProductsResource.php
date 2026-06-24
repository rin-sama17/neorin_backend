<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $discount = $this->getActiveDiscount();

        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'material'         => $this->material,
            'slug'             => $this->slug,
            'tags'             => $this->tags,
            'status'           => $this->status,
            'is_special'       => $this->is_special,
            'view'             => $this->view,
            'image'            => $this->image,

            // قیمت پایه + تخفیف
            'price'            => $this->price,
            'discount'         => $discount ? [
                'value'       => $discount->value,
                'final_price' => $discount->calculateFinalPrice($this->price),
            ] : null,

            // سایزها با قیمت و تخفیف
           'sizes' => $this->sizes->map(function ($size) use ($discount) {
                    $originalPrice = $size->price ?? $this->price;
                    return [
                        'id'             => $size->id,
                        'name'           => $size->name,
                        'width'          => $size->width,
                        'height'         => $size->height,
                        'stock'          => $size->stock,
                        'image'          => $size->image,
                        'original_price' => $originalPrice,
                        'discount'       => $discount ? [
                            'value'       => $discount->value,
                            'final_price' => $discount->calculateFinalPrice($originalPrice),
                        ] : null,
                    ];
                }),
            
            // روابط
            'category'         => $this->category,
            'city'             => $this->city,
            'user'             => $this->user,
            'fabrics'          => $this->fabrics,
            'colors'           => $this->colors,

            // SEO
            'seo' => [
                'meta_title'       => $this->meta_title ?? $this->title,
                'meta_description' => $this->meta_description ?? $this->description,
            ],

            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }

    public function with($request): array
    {
        return ['status' => true];
    }
}