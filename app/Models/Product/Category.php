<?php

namespace App\Models\Product;

use Cviebrock\EloquentSluggable\Sluggable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product\Products;
use App\Models\Product\CategoryAttribute;

class Category extends Model
{
    use SoftDeletes, Sluggable, CascadeSoftDeletes;

    protected $cascadeDeletes = ['children'];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    protected $guarded = ["id", 'slug'];

    public function children()
    {
        return $this->hasMany($this, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo($this, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Products::class);
    }
    public function attributes()
    {
        return $this->hasMany(CategoryAttribute::class);
    }
}
