<?php

namespace App\Models\Product;

use App\Models\Geo\City;
use App\Models\User;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
   use SoftDeletes, CascadeSoftDeletes;

    protected $guarded=["id"];

    public function category(){
        return $this->belongsTo(Category::class);
    }
     public function city(){
        return $this->belongsTo(City::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
