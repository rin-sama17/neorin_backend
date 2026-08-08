<?php

namespace App\Models\Product;

use Cviebrock\EloquentSluggable\Sluggable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalculationProfile extends Model
{
    use SoftDeletes, Sluggable;

    protected $guarded = ['id'];

    protected $casts = [
        'config'         => 'array',
        'profit_percent' => 'float',
        'vat_percent'    => 'float',
        'is_active'      => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function formulas()
    {
        return $this->hasMany(Formula::class)->orderBy('priority');
    }

    public function activeFormulas()
    {
        return $this->formulas()->where('is_active', true);
    }

    public function customProductItems()
    {
        return $this->hasMany(CustomProductItem::class);
    }

    public function getLaborConfig(): array
    {
        return $this->config['labor'] ?? [];
    }

    public function getFiberConfig(): array
    {
        return $this->config['fiber'] ?? [];
    }

    public function getExtraConfig(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }
}
