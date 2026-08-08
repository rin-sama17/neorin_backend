<?php

namespace App\Models\Product;

use Cviebrock\EloquentSluggable\Sluggable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomProduct extends Model
{
    use SoftDeletes, Sluggable;

    protected $guarded = ['id'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function items()
    {
        return $this->hasMany(CustomProductItem::class)->orderBy('sort');
    }

    public function activeItems()
    {
        return $this->items()->where('status', 1);
    }
}
