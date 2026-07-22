<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'item_type'     => $this->item_type,
            'quantity'      => $this->quantity,
            'unit_price'    => $this->unit_price,
            'total_price'   => $this->total_price,
            'snapshot'      => $this->snapshot,       // اطلاعات اصلی خرید
            'configuration' => $this->configuration,  // انتخاب‌های کاربر
        ];
    }
}
