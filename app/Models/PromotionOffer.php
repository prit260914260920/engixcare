<?php

namespace App\Models;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PromotionOffer extends Model
{
    protected $table = 'promotion_offers';

    protected $fillable = [
        'title',
        'type',
        'description',
        'discount_text',
        'coupon_code',
        'percentage',
        'target_amount',
        'is_active',
        'for_public',
        'sort_order',
    ];

    protected $casts = [
        'percentage' => 'integer',
        'target_amount' => 'integer',
        'is_active' => 'boolean',
        'for_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('for_public', true)->orderBy('sort_order')->orderBy('id');
    }

    public function calculateDiscount(float $subtotal): int
    {
        if (!$this->percentage || $this->percentage <= 0) {
            return 0;
        }

        return (int) round($subtotal * ($this->percentage / 100));
    }

    public function isApplicableForOrder(float $subtotal, ?User $user): bool
    {
        if (!$this->is_active || !$this->for_public) {
            return false;
        }

        $isValid = true;

        if ($this->type === 'first_order' && $user !== null) {
            $isValid = !Order::where('user_id', $user->id)->exists();
        }

        if ($this->type === 'amount_based' && $this->target_amount !== null) {
            $isValid = $isValid && $subtotal >= $this->target_amount;
        }

        return $isValid;
    }
}
