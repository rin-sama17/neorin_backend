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
    use SoftDeletes, Sluggable, CascadeSoftDeletes;

    protected $guarded = ['id'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                "source" => "title"
            ]
        ];
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function products()
    {
        return $this->belongsToMany(Products::class, 'fabric_product')->withTimestamps();
    }
}
