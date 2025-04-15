<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product\Products;

class Gallery extends Model
{

    use SoftDeletes;

    protected $guarded = ["id"];
    protected function casts(): array
    {
        return [
            'image' => 'array',
        ];
    }
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
