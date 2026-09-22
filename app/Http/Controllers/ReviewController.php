<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('product')->latest();

        // Filter by star rating
        if ($request->filled('stars')) {
            $query->where('stars', $request->stars);
        }

        // Filter by visibility
        if ($request->filled('visible')) {
            $query->where('is_visible', $request->boolean('visible'));
        }

        // Search by reviewer name, email, or body
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('name',  'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('body',  'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%");
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        // Summary stats
        $stats = [
            'total'   => Review::count(),
            'visible' => Review::where('is_visible', true)->count(),
            'hidden'  => Review::where('is_visible', false)->count(),
            'avg'     => round(Review::avg('stars') ?? 0, 1),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function toggleVisible(Review $review)
    {
        $review->update(['is_visible' => ! $review->is_visible]);

        return response()->json([
            'is_visible' => $review->is_visible,
            'message'    => $review->is_visible ? 'Review is now visible.' : 'Review is now hidden.',
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
