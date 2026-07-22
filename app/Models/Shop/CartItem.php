<?php

namespace App\Models\Shop;

use App\Models\Product\Fabric;
use App\Models\Product\Products;
use App\Models\Product\Size;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $guarded = ['id'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class)->withTrashed();
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function fabrics()
    {
        return $this->belongsToMany(Fabric::class, 'cart_item_fabrics');
    }

    public function categoryValues()
    {
        return $this->hasMany(CartItemCategoryValue::class, 'cart_item_id');
    }
}
