<?php

namespace App\Models\Shop;

use App\Models\Shop\Order;
use App\Models\Product\Products;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = ['id'];
    protected $casts   = [
        'snapshot'      => 'array',
        'configuration' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->belongsTo(Products::class)->withTrashed();
    }
}
