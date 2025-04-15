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
        return [
            'id' => $this->id,
            "title" => $this->title,
            'description' => $this->description,
            'product_type' => $this->product_type,
            'product_status' => $this->product_status,
            'allCategories' => $this->getAllCategories($this->category),
            'category' => $this->category,
            'city' => $this->city,
            "status" => $this->status,
            'published_at' => $this->published_at,
            'expierd_at' => $this->expierd_at,
            'view' => $this->view,
            'contact' => $this->contact,
            'is_special' => $this->is_special,
            'is_ladder' => $this->is_ladder,
            'image' => $this->image,
            'gallery' => $this->gallery->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image
                ];
            }),
            'category_attributes' => $this->category->attributes->map(function ($attribute) {
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
            'category_attribute_with_values' => $this->category->attributes->map(function ($attribute) {
                $value = $this->categoryValues->firstWhere('category_attribute_id', $attribute->id);
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'unit' => $attribute->unit,
                    'value' => $value ? $value->value : null,
                ];
            }),
            'slug' => $this->slug,
            'price' => $this->price,
            'tags' => $this->tags,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'willing_to_trade' => $this->willing_to_trade,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
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
