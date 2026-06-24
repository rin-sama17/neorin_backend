<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product\Products;

class Size extends Model
{
    use SoftDeletes;

    protected $guarded = ["id"];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
    
    
}
