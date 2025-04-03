<?php

namespace App\Models\Product;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryAttribute extends Model
{
  use SoftDeletes, CascadeSoftDeletes;

    protected $guarded=["id"];

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
