<?php

namespace App\Models\Product;

use Cviebrock\EloquentSluggable\Sluggable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes, Sluggable, CascadeSoftDeletes;

    protected $cascadeDeletes = ['children'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    protected $guarded = ['id', 'slug'];

    public function children()
    {
        return $this->hasMany($this, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo($this, 'parent_id');
    }

    public function fabric()
    {
        return $this->hasMany(Fabric::class);
    }

    public function products()
    {
        return $this->hasMany(Products::class);
    }

    public function attributes()
    {
        return $this->hasMany(CategoryAttribute::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class, 'category_id');
    }

    public function customProductTypes()
    {
        return $this->hasMany(CustomProductType::class);
    }

    public function allAttributes()
    {
        $attrs = $this->attributes()->with('categoryValues')->get();

        if ($this->parent_id) {
            $parent = static::with('attributes.categoryValues')->find($this->parent_id);
            if ($parent) {
                $attrs = $attrs->merge($parent->allAttributes());
            }
        }

        return $attrs->unique('id');
    }

    public function checkoutAttributes()
    {
        return $this->allAttributes()
            ->where('type', 1)
            ->map(fn($attribute) => [
                'id'     => $attribute->id,
                'name'   => $attribute->name,
                'unit'   => $attribute->unit,
                'type'   => $attribute->type,
                'values' => $attribute->categoryValues->map(fn($value) => [
                    'id'    => $value->id,
                    'value' => $value->value,
                    'price' => $value->price,
                ])->values(),
            ])->values();
    }

    public function customProductAttributes()
    {
        return $this->allAttributes()
            ->where('selection_type', 1)
            ->map(fn($attribute) => [
                'id'           => $attribute->id,
                'name'         => $attribute->name,
                'unit'         => $attribute->unit,
                'type'         => $attribute->type,
                'is_required'  => $attribute->is_required,
                'min_value'    => $attribute->min_value,
                'max_value'    => $attribute->max_value,
                'default_value' => $attribute->default_value,
                'values'       => $attribute->categoryValues
                    ->where('status', 1)
                    ->map(fn($value) => [
                        'id'    => $value->id,
                        'value' => $value->value,
                        'price' => $value->price,
                    ])->values(),
            ])->values();
    }
}
