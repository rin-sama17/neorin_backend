<?php

namespace App\Models\Product;

use Cviebrock\EloquentSluggable\Sluggable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formula extends Model
{
    use SoftDeletes, Sluggable;

    protected $guarded = ['id'];

    protected $casts = [
        'config'     => 'array',
        'is_active'  => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function calculationProfile()
    {
        return $this->belongsTo(CalculationProfile::class);
    }
}
