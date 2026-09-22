<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroSection extends Model
{
    protected $fillable = [
        'pill_text',
        'title_main',
        'title_gradient',
        'subtitle',
        'cta_primary_label',
        'cta_primary_url',
        'cta_secondary_label',
        'cta_secondary_url',
        'minerals',
        'trust_badges',
        'badge_rating',
        'badge_lab',
        'image_path',
    ];

    protected $casts = [
        'minerals'    => 'array',
        'trust_badges' => 'array',
    ];

    /**
     * Always returns the single hero section row, creating it with defaults if missing.
     */
    public static function current(): self
    {
        return static::firstOrCreate([], [
            'pill_text'             => 'WHO-GMP & HACCP Certified · FSSAI Approved · Effervescent Technology',
            'title_main'            => 'Hydrate. Restore.',
            'title_gradient'        => 'Recharge.',
            'subtitle'              => 'Boost Energy — Orange flavour effervescent health supplement. Drop 1 tablet in 200 ml water, dissolve completely, and enjoy a tangy orange drink. Tastes best with chilled water. Pack of 15 tablets.',
            'cta_primary_label'     => 'Buy Now',
            'cta_primary_url'       => '#products',
            'cta_secondary_label'   => 'Explore Products',
            'cta_secondary_url'     => '#products',
            'minerals'              => [
                ['label' => 'Chloride',   'value' => '220 mg'],
                ['label' => 'Magnesium',  'value' => '56 mg'],
                ['label' => 'Potassium',  'value' => '115 mg'],
                ['label' => 'Calcium',    'value' => '100 mg'],
            ],
            'trust_badges'          => [
                ['icon' => 'fa-solid fa-certificate',    'label' => 'FSSAI Certified'],
                ['icon' => 'fa-solid fa-industry',       'label' => 'WHO-GMP & HACCP'],
                ['icon' => 'fa-solid fa-truck-fast',     'label' => 'Free Shipping · COD'],
                ['icon' => 'fa-solid fa-shield-halved',  'label' => 'Lab Tested'],
            ],
            'badge_rating'          => '4.9/5 · 12k+ Reviews',
            'badge_lab'             => 'Lab Tested',
            'image_path'            => null,
        ]);
    }

    /**
     * Returns the public URL for the hero image, falling back to the default asset.
     */
    public function imageUrl(): string
    {
        if ($this->image_path) {
            return Storage::disk('public')->url($this->image_path);
        }

        return asset('asset/210A0234.png');
    }
}
