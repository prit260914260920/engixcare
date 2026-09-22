<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::first();
        if (!$product) {
            return;
        }

        $reviews = [
            [
                'name'      => 'Aditi Sharma',
                'email'     => 'aditi.sharma@example.com',
                'title'     => 'Energy boost every morning',
                'body'      => 'Amazing boost of energy and the flavor is very pleasant. I feel refreshed every morning.',
                'stars'     => 5,
                'is_visible' => true,
            ],
            [
                'name'      => 'Rohit Mehta',
                'email'     => 'rohit.mehta@example.com',
                'title'     => 'Daily staple for immunity',
                'body'      => 'The effervescent tablet dissolves quickly and has become my daily staple. Highly recommend for immunity support.',
                'stars'     => 5,
                'is_visible' => true,
            ],
            [
                'name'      => 'Sana Khan',
                'email'     => 'sana.khan@example.com',
                'title'     => 'Great taste and results',
                'body'      => 'Works well and tastes good, although I wish the bottle had more tablets.',
                'stars'     => 4,
                'is_visible' => true,
            ],
        ];

        foreach ($reviews as $review) {
            Review::create(array_merge($review, ['product_id' => $product->id]));
        }
    }
}
