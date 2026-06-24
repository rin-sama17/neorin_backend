<?php

namespace App\Models\Product;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product\Products;
use App\Models\Product\Fabric;

class Color extends Model
{
    use SoftDeletes, CascadeSoftDeletes ;
  

    protected $guarded = ['id'];

 
    public function products()
    {
        return $this->belongsToMany(Products::class, 'color_product' ,'color_id', 'product_id')->withTimestamps();
    }
     public function fabrics()
    {
        return $this->belongsToMany(Fabric::class, 'color_fabric' ,'color_id', 'fabric_id')->withTimestamps();
    }
}
