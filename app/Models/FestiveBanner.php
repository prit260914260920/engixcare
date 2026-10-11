<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FestiveBanner extends Model
{
    protected $table = 'festive_banners';

    protected $fillable = [
        'name',
        'banner_type',          // 'custom' | 'image_only'
        'tag_line',
        'title',
        'offer_label',
        'discount_text',
        'sub_text',
        'fine_print',
        'cta_label',
        'cta_url',
        'bg_image_path',
        'product_image_path',
        'accent_image_path',
        'full_image_path',      // used only for image_only type
        'full_image_url',       // optional click-through URL for image_only
        'bg_color_from',
        'bg_color_to',
        'text_color',
        'discount_bg_color',
        'discount_text_color',
        'is_active',
        'start_date',
        'end_date',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'start_date' => 'date',
        'end_date'   => 'date',
        'sort_order' => 'integer',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    /**
     * Returns banners that are active and within their validity window (if set).
     */
    public function scopeVisible($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
            })
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }

    // ── Image helpers ─────────────────────────────────────────────────────────

    public function bgImageUrl(): ?string
    {
        return $this->bg_image_path
            ? Storage::disk('public')->url($this->bg_image_path)
            : null;
    }

    public function productImageUrl(): ?string
    {
        return $this->product_image_path
            ? Storage::disk('public')->url($this->product_image_path)
            : null;
    }

    public function accentImageUrl(): ?string
    {
        return $this->accent_image_path
            ? Storage::disk('public')->url($this->accent_image_path)
            : null;
    }

    public function fullImageUrl(): ?string
    {
        return $this->full_image_path
            ? Storage::disk('public')->url($this->full_image_path)
            : null;
    }

    public function isImageOnly(): bool
    {
        return $this->banner_type === 'image_only';
    }

    /**
     * CSS gradient string for the banner background.
     */
    public function gradientStyle(): string
    {
        return "background: linear-gradient(135deg, {$this->bg_color_from} 0%, {$this->bg_color_to} 100%);";
    }
}
