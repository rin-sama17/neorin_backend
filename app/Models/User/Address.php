<?php

namespace App\Models\User;

use App\Models\Geo\City;
use App\Models\Product\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'state_id',
        'city_id',
        'address',
        'plaque',
        'unit',
        'postal_code',
        'title',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Address $address) {
            if ($address->is_default) {
                static::where('user_id', $address->user_id)
                    ->when($address->exists, fn($q) => $q->whereKeyNot($address->id))
                    ->update(['is_default' => false]);
            }
        });

        static::deleted(function (Address $address) {
            if ($address->is_default) {
                static::where('user_id', $address->user_id)
                    ->latest()
                    ->first()
                    ?->update(['is_default' => true]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
