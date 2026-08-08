<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomProductRule extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'payload'  => 'array',
        'status'   => 'boolean',
    ];

    public function customProductItem()
    {
        return $this->belongsTo(CustomProductItem::class);
    }

    public function parentRule()
    {
        return $this->belongsTo(CustomProductRule::class, 'parent_rule_id');
    }

    public function childRules()
    {
        return $this->hasMany(CustomProductRule::class, 'parent_rule_id')->orderBy('priority');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_rule_id');
    }

    public function scopeForItem($query, int $itemId)
    {
        return $query->where('custom_product_item_id', $itemId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
