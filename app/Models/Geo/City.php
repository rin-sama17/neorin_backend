<?php

namespace App\Models\Geo;

use App\Models\Product\Products;
use App\Models\Product\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function products()
    {
        return $this->hasMany(Products::class);
    }

    protected function scopeActive(Builder $query): void
    {
        $query->where('status', 1);
    }
}
