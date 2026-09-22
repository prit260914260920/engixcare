<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'pincode',
        'items',
        'coupon_code',
        'subtotal',
        'discount',
        'gst',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'notes',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
    ];

    protected function casts(): array
    {
        return [
            'items'    => 'array',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'gst'      => 'decimal:2',
            'total'    => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Human-readable order number, e.g. ENG-000042 */
    public function getOrderNumberAttribute(): string
    {
        return 'ENG-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
