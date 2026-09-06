<?php

namespace App\Models\Shop;

use App\Models\User;
use App\Models\Shop\OrderItem;
use App\Models\Shop\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $casts   = [
        'shipping_address_snapshot' => 'array',
        'paid_at'                   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function payments()
    {
        return $this->hasOne(Payment::class);
    }

    protected static function booted()
    {
        static::updating(function (Order $order) {
            foreach (['subtotal', 'total_price', 'user_id'] as $field) {
                if ($order->isDirty($field)) {
                    throw new \Exception("فیلد {$field} قابل تغییر نیست.");
                }
            }
        });
    }
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeStatus($query, ?string $status)
    {
        return $query->when($status && $status !== 'all', fn($q) => $q->where('order_status', $status));
    }
}
