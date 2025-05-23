<?php

namespace App\Models\Product;

use App\Models\Product\CategoryAttribute;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product\Products;

class CategoryValue extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $guarded = ["id"];

    public function categoryAttribute()
    {
        return $this->belongsTo(CategoryAttribute::class);
    }

    public function products()
    {
        return $this->belongsToMany(Products::class, 'product_category_value')->withTimestamps();
    }
}
