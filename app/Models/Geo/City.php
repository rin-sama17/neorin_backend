<?php

namespace App\Models\Geo;

use App\Models\Product\Products;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
   use SoftDeletes;

   public function products()
    {
        return $this->hasMany(Products::class);
    }

}
