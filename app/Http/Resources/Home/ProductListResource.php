<?php

namespace App\Http\Resources\Home;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'price' => $this->price,
            'final_price' => $this->finalPrice(),
            'has_discount' => $this->discount !== null,
            'stock' => $this->stock,
            'is_special' => (bool) $this->is_special,
            'image' => $this->image,
            'gallery' => $this->gallery ?? [],
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'sizes' => $this->whenLoaded('sizes', fn() => $this->sizes->map(fn($s) => [
                'id' => $s->id,
                'width' => $s->width,
                'height' => $s->height,
            ])),
            'fabrics' => $this->whenLoaded('fabrics', fn() => $this->fabrics->take(3)->map(fn($f) => [
                'id' => $f->id,
                'title' => $f->title,
                'image' => $f->image['indexArray']['small'] ?? null,
                'price_diff' => (float) $f->price - $this->price,
            ])),
        ];
    }

    protected function finalPrice(): int
    {
        if (! $this->discount) return $this->price;
        return $this->discount->type === 'percent'
            ? (int) round($this->price - ($this->price * $this->discount->amount / 100), -3)
            : (int) round($this->price - $this->discount->amount, -3);
    }
}
