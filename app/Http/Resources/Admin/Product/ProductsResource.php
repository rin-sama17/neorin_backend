<?php

namespace App\Http\Resources\Admin\Product;

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
            'category' => $this->category,
            'user' => $this->user,
            'city' => $this->city,
            "status" => $this->status,
            'published_at' => $this->published_at,
            'expierd_at' => $this->expierd_at,
            'view' => $this->view,
            'contact' => $this->contact,
            'is_special' => $this->is_special,
            'is_ladder' => $this->is_ladder,
            'image' => $this->image,
            'gallery' => $this->gallery,
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


    public function with($request)
    {
        return [
            'statue' => true,
        ];
    }
}
