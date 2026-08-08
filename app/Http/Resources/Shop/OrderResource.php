<?php

namespace App\Http\Resources\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                       => $this->id,
            'order_status'             => $this->order_status,
            'payment_status'           => $this->payment_status,
            'payment_method'           => $this->payment_method,
            'payment_ref'              => $this->payment_ref,
            'paid_at'                  => $this->paid_at,
            'tracking_code'            => $this->tracking_code,
            'notes'                    => $this->notes,
            'subtotal'                 => $this->subtotal,
            'discount'                 => $this->discount,
            'shipping_price'           => $this->shipping_price,
            'total_price'              => $this->total_price,
            'created_at'               => $this->created_at,
            'shipping_address_snapshot' => $this->shipping_address_snapshot,
            'items'                    => $this->whenLoaded('items'),
        ];
    }
}
