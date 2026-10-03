<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    /**
     * Fulfilment status values.
     *
     * Local-only:
     *   placed       → order created, not yet pushed to Shiprocket
     *   processing   → pushed to Shiprocket, awaiting pickup
     *
     * Synced from Shiprocket (shipments.status / scheduler):
     *   pending           → order confirmed at Shiprocket, courier not yet assigned
     *   order_received    → courier has received the shipment
     *   order_picked      → picked up from seller
     *   in_transit        → in transit to destination
     *   out_for_delivery  → out for delivery to customer
     *   reached           → reached destination / delivered
     *
     * Terminal (no more polling):
     *   delivered  → successfully delivered
     *   cancelled  → order cancelled
     */
    public const STATUSES = [
        'placed',
        'processing',
        'pending',
        'order_received',
        'order_picked',
        'in_transit',
        'out_for_delivery',
        'reached',
        'delivered',
        'cancelled',
    ];

    /**
     * Statuses where we should STOP polling Shiprocket.
     */
    public const TERMINAL_STATUSES = ['reached', 'delivered', 'cancelled'];

    /**
     * Map Shiprocket shipment status strings → our local status.
     */
    public const SHIPROCKET_STATUS_MAP = [
        'PENDING'           => 'pending',
        'NEW'               => 'pending',
        'PICKUP SCHEDULED'  => 'pending',
        'PICKUP GENERATED'  => 'pending',
        'PICKUP QUEUED'     => 'pending',
        'MANIFESTED'        => 'order_received',
        'PICKUP ERROR'      => 'order_received',
        'PICKED UP'         => 'order_picked',
        'IN TRANSIT'        => 'in_transit',
        'REACHED WAREHOUSE' => 'in_transit',
        'MISROUTED'         => 'in_transit',
        'OUT FOR DELIVERY'  => 'out_for_delivery',
        'REACHED DESTINATION' => 'reached',
        'DELIVERED'         => 'delivered',
        'CANCELLED'         => 'cancelled',
        'RTO INITIATED'     => 'cancelled',
        'RTO IN TRANSIT'    => 'cancelled',
        'RTO DELIVERED'     => 'cancelled',
    ];

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
        'shiprocket_order_id',
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
