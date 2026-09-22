<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'email',
        'title',
        'body',
        'stars',
        'is_visible',
    ];

    protected $casts = [
        'stars'      => 'integer',
        'is_visible' => 'boolean',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
