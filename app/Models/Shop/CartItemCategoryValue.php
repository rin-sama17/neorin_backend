<?php

namespace App\Models\Shop;

use App\Models\Product\CategoryAttribute;
use App\Models\Product\CategoryValue;
use Illuminate\Database\Eloquent\Model;

class CartItemCategoryValue extends Model
{
    protected $table   = 'cart_item_category_value';
    protected $guarded = ['id'];

    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    public function attribute()
    {
        return $this->belongsTo(CategoryAttribute::class, 'category_attribute_id');
    }

    public function categoryValue()
    {
        return $this->belongsTo(CategoryValue::class, 'category_value_id');
    }
}
