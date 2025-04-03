<?php

namespace App\Models\Content;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes, Sluggable;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
                ]
            ];
        }
        protected $guarded=["id", 'slug'];
}
