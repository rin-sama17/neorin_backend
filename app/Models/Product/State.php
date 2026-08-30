<?php

namespace App\Models\Product;

use App\Models\Geo\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function cities()
    {
        return $this->belongsTo(City::class);
    }

    public function products()
    {
        return $this->hasMany(Products::class);
    }
}
