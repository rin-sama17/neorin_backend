<?php

namespace App\Models\Product;

use Cviebrock\EloquentSluggable\Sluggable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product\Products;
use App\Models\Product\Category;

class Fabric extends Model
{
    use SoftDeletes, CascadeSoftDeletes , Sluggable;

    protected $guarded = ['id'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                "source" => "title"
            ]
        ];
    }

    protected function casts(): array
    {
        return [
            'image' => 'array',
        ];
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function products()
    {
        return $this->belongsToMany(Products::class, 'fabric_product' ,'fabric_id', 'product_id')->withTimestamps();
    }
     public function colors()
    {
        return $this->belongsToMany(Color::class, 'color_fabric' ,'fabric_id', 'color_id')->withTimestamps();
    }
}
