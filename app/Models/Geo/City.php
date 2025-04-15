<?php

namespace App\Models\Geo;

use App\Models\Product\Products;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class City extends Model
{
    use SoftDeletes;

    public function products()
    {
        return $this->hasMany(Products::class);
    }



    protected function scopeActive(Builder $query): void

    {
        $query->where('status', 1);
    }
}
