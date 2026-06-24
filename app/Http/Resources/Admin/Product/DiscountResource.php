<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return    
        [
            'id' => $this->id,
            // 'product' => $this->product,
            'category' => $this->category,
            'value' =>$this->value,
            'starts_at' =>$this->starts_at,
            'end_at' =>$this->end_at,
            'is_active' =>$this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
