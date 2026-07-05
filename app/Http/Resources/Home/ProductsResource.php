<?php

namespace App\Http\Resources\Home;

use App\Models\Product\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
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
            'stock'          => $this->stock,

            // قیمت پایه + تخفیف
            'price'            => $this->price,
            'discount'         => $discount ? [
                "id"           => $discount->id,
                'product_id'  => $discount->product?->id ?? null,
                'category_id' => $discount->category?->id ?? null,
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
            'gallery' => $this->gallery->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image
                ];
            }),
            'allCategories' => $this->getAllCategories($this->category),
            'category' => $this->category,
            'category_attributes' => $this->category?->attributes->map(function ($attribute) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'unit' => $attribute->unit,

                ];
            }),
            'category_values' => $this->categoryValues->map(function ($value) {
                return [
                    'id' => $value->id,
                    'value' => $value->value,
                ];
            }),
        'checkout_attributes' => $this->category?->checkoutAttributes(),
    
            'category_attribute_with_values' => $this->category?->attributes->map(function ($attribute) {
                $value = $this->categoryValues->firstWhere('category_attribute_id', $attribute->id);
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'unit' => $attribute->unit,
                    'value' => $value ? $value->value : null,
                ];
            }),
            'city'             => $this->city,
            'user'             => $this->user,
             'fabrics' => $this->fabrics->map(function ($fabric) {
                return [
                    "id" => $fabric->id,
            'title' => $fabric->title,
            "material" => $fabric->material,
            "image" => $fabric->image,
            "colors" => $fabric->colors,
            'slug' => $fabric->slug,
            'category' => $fabric->category,
            'products'=>$fabric->products,
            'price' => $fabric->price,
            'status' => $fabric->status,
            'created_at' => $fabric->created_at,
            'updated_at' => $fabric->updated_at,
                    
                ];
            }),
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

    private function getAllCategories($category)
    {
        $all = [];
        while ($category) {
            $all[] = [
                'id' => $category->id,
                'name' => $category->name,
            ];
            $category = $category->parent;
        }
        return array_reverse($all);
    }

    public function with($request)
    {
        return [
            'statue' => true,
        ];
    }
}
