<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormulaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'calculation_profile_id' => $this->calculation_profile_id,
            'name'                  => $this->name,
            'slug'                  => $this->slug,
            'formula_type'          => $this->formula_type,
            'config'                => $this->config,
            'priority'              => $this->priority,
            'is_active'             => $this->is_active,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
