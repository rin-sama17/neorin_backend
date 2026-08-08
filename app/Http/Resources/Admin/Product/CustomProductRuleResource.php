<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomProductRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'custom_product_item_id' => $this->custom_product_item_id,
            'group_id'               => $this->group_id,
            'parent_rule_id'         => $this->parent_rule_id,
            'condition_type'         => $this->condition_type,
            'condition_source'       => $this->condition_source,
            'condition_operator'     => $this->condition_operator,
            'condition_value'        => $this->condition_value,
            'condition_reference_id' => $this->condition_reference_id,
            'action'                 => $this->action,
            'target_type'            => $this->target_type,
            'target_id'              => $this->target_id,
            'payload'                => $this->payload,
            'priority'               => $this->priority,
            'status'                 => $this->status,
            'child_rules'            => $this->whenLoaded('childRules', fn() =>
                CustomProductRuleResource::collection($this->childRules)
            ),
            'parent_rule'            => $this->whenLoaded('parentRule', fn() => [
                'id'     => $this->parentRule->id,
                'action' => $this->parentRule->action,
            ]),
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
        ];
    }
}
