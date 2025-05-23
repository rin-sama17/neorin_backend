<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
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
            'description' => $this->description,
            'status' => $this->status,
            'parent_id' => $this->parent_id,
            'children' => $this->children,
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
