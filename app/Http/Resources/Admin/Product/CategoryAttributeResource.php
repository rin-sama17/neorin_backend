<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryAttributeResource extends JsonResource
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
            'name' => $this->name,
            'unit' => $this->unit,
            'category' => $this->category,
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
