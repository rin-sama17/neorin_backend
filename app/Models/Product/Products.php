<?php

namespace App\Models\Product;

use App\Models\Geo\City;
use App\Models\User;
use App\Models\Product\CategoryValue;
use Cviebrock\EloquentSluggable\Sluggable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product\Gallery;

class Products extends Model
{
    use SoftDeletes, Sluggable, CascadeSoftDeletes;

    protected $guarded = ["id"];
    protected $cascadeDeletes = ['gallery'];


    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
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
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function viewedByUsers()
    {
        return $this->belongsToMany(User::class, 'products_view_history')->withTimestamps();
    }
    public function categoryValues()
    {
        return $this->belongsToMany(CategoryValue::class, 'product_category_value')->withTimestamps();
    }

    public function gallery()
    {
        return $this->hasMany(Gallery::class, 'product_id');
    }
}
