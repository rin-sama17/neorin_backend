<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryValueResource extends JsonResource
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
            'value' => $this->value,
            'categoryAttribute' => $this->categoryAttribute,
            'type' =>$this->type,
            'status' =>$this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }


    public function with($request){
        return [
            'statue'=>true,
        ];

    }
}
