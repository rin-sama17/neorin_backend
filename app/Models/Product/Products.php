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
use App\Models\Product\Fabric;

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
    public function sizes()
    {
        return $this->hasMany(Size::class, 'product_id');
    }
    public function gallery()
    {
        return $this->hasMany(Gallery::class, 'product_id');
    }
    public function fabrics()
    {
        return $this->belongsToMany(Fabric::class, 'fabric_product', 'product_id', 'fabric_id')->withTimestamps();
    }
     public function colors()
    {
        return $this->belongsToMany(Color::class, 'color_product' ,'product_id', 'color_id')->withTimestamps();
    }
  
    public function discounts (){
        return $this->hasMany(Discount::class, 'product_id');
    }
   public function getActiveDiscount(): ?Discount
{
    $now = now();

    $activeQuery = fn($q) => $q
        ->where('is_active', true)
        ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
        ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', $now));

    $discount = $this->discounts()->where($activeQuery)->latest()->first();

    if (!$discount) {
        $discount = Discount::where('category_id', $this->category_id)
            ->whereNull('product_id')
            ->where($activeQuery)
            ->latest()
            ->first();
    }

    return $discount;
}
}
