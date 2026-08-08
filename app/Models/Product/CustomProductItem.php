<?php

namespace App\Models\Product;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomProductItem extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function customProduct()
    {
        return $this->belongsTo(CustomProduct::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function calculationProfile()
    {
        return $this->belongsTo(CalculationProfile::class);
    }

    public function rules()
    {
        return $this->hasMany(CustomProductRule::class)->orderBy('priority');
    }

    public function topRules()
    {
        return $this->rules()->topLevel()->active()->orderBy('priority');
    }

    public function getAttributesConfig()
    {
        return $this->category->customProductAttributes();
    }

    public function getFabrics()
    {
        return $this->category->fabric()->where('status', 1)->get();
    }
}
