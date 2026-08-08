<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalculationProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'strategy_type'  => $this->strategy_type,
            'config'         => $this->config,
            'profit_percent' => $this->profit_percent,
            'vat_percent'    => $this->vat_percent,
            'is_active'      => $this->is_active,
            'formulas'       => $this->whenLoaded('formulas', fn() =>
                FormulaResource::collection($this->formulas)
            ),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
