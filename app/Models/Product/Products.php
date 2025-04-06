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
  protected function casts(): array
    {
        return [
            'image' => 'array',
        ];
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }
     public function city(){
        return $this->belongsTo(City::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }

       public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

       public function viewedByUsers()
    {
        return $this->belongsToMany(User::class ,'products_view_history')->withTimestamps();
    }
}
