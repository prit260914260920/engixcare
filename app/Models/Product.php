<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Cart;

class Product extends Model
{
    protected $fillable = [
        'name',
        'subtitle',
        'sku',
        'category',
        'price',
        'original_price',
        'discounted_price',
        'stock',
        'status',
        'featured',
        'is_visible',
        'show_stock',
        'image',
        'video',
        'image_padding_top',
        'image_padding_right',
        'image_padding_bottom',
        'image_padding_left',
        'description',
        // card fields
        'badge_label',
        'badge_color',
        'rating',
        'review_count',
        'bullet_points',
        'warning_text',
        'stock_label',
        'unit_value',
        'unit_label',
    ];

    protected $casts = [
        'image'         => 'array',
        'video'         => 'array',
        'bullet_points' => 'array',
        'featured'      => 'boolean',
        'is_visible'    => 'boolean',
        'show_stock'    => 'boolean',
        'rating'           => 'float',
        'review_count'     => 'integer',
        'unit_value'       => 'float',
        'original_price'   => 'float',
        'discounted_price' => 'float',
    ];

    public function ingredient(): HasOne
    {
        return $this->hasOne(Ingredient::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
