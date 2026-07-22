<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'order_status'     => $this->order_status,
            'payment_status'   => $this->payment_status,
            'subtotal'         => $this->subtotal,
            'shipping_price'   => $this->shipping_price,
            'discount'         => $this->discount,
            'total_price'      => $this->total_price,
            'tracking_code'    => $this->tracking_code,
            'shipping_address' => $this->shipping_address_snapshot,
            'items'            => OrderItemResource::collection($this->items),
            'paid_at'          => $this->paid_at,
            'created_at'       => $this->created_at,
        ];
    }
}
