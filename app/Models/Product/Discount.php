<?php

namespace App\Models\Product;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product\Products;
use App\Models\Product\Category;


class Discount extends Model
{
    use SoftDeletes, CascadeSoftDeletes ;
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    protected $guarded = ['id'];
    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function calculateFinalPrice(float $originalPrice): float
    {
        $final_price =  $originalPrice - ($originalPrice * $this->value / 100); 
        return round($final_price / 1000) * 1000;
    }
}
