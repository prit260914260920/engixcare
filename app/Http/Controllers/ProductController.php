<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // ── Shared validation rules ───────────────────────────────────────────────
    private function rules(int $ignoreId = 0): array
    {
        return [
            'name'          => 'required|string|max:255',
            'subtitle'      => 'nullable|string|max:255',
            'sku'           => 'required|string|max:255|unique:products,sku,' . $ignoreId,
            'category'      => 'nullable|string|max:255',
            'price'             => 'required|numeric|min:0',
            'original_price'    => 'nullable|numeric|min:0',
            'discounted_price'  => 'nullable|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'status'        => 'required|string|in:Active,Inactive',
            'featured'      => 'nullable|boolean',
            'is_visible'    => 'nullable|boolean',
            'show_stock'    => 'nullable|boolean',
            'image_paths'   => 'nullable|string',
            'video_paths'   => 'nullable|string',
            'image_padding_top' => 'nullable|integer|min:-200|max:200',
            'image_padding_right' => 'nullable|integer|min:-200|max:200',
            'image_padding_bottom' => 'nullable|integer|min:-200|max:200',
            'image_padding_left' => 'nullable|integer|min:-200|max:200',
            'description'   => 'nullable|string',
            // card fields
            'badge_label'   => 'nullable|string|max:100',
            'badge_color'   => 'nullable|string|in:orange,green,blue,red,purple',
            'rating'        => 'nullable|numeric|min:0|max:5',
            'review_count'  => 'nullable|integer|min:0',
            'bullet_points' => 'nullable|string',
            'warning_text'  => 'nullable|string|max:500',
            'stock_label'   => 'nullable|string|max:255',
            'unit_value'    => 'nullable|numeric|min:0',
            'unit_label'    => 'nullable|string|max:100',
        ];
    }

    private function prepare(Request $request): array
    {
        $data = $request->except(['_token', '_method', 'bullet_points', 'image_paths', 'video_paths']);
        $data['featured']      = $request->boolean('featured');
        $data['is_visible']    = $request->boolean('is_visible');
        $data['show_stock']    = $request->boolean('show_stock');
        $data['bullet_points'] = $this->parseBullets($request->input('bullet_points', ''));
        $data['image_padding_top'] = (int) $request->input('image_padding_top', 0);
        $data['image_padding_right'] = (int) $request->input('image_padding_right', 0);
        $data['image_padding_bottom'] = (int) $request->input('image_padding_bottom', 0);
        $data['image_padding_left'] = (int) $request->input('image_padding_left', 0);

        // image_paths is a JSON array of storage paths sent by the JS uploader
        $raw           = $request->input('image_paths', '[]');
        $data['image'] = json_decode($raw, true) ?? [];

        // video_paths is a JSON array of storage paths sent by the JS video uploader
        $rawVideo       = $request->input('video_paths', '[]');
        $data['video']  = json_decode($rawVideo, true) ?? [];

        return $data;
    }

    /** Convert newline-separated textarea value to JSON array, skip blanks. */
    private function parseBullets(?string $raw): array
    {
        if (!$raw) return [];
        return array_values(array_filter(
            array_map('trim', explode("\n", str_replace("\r", '', $raw)))
        ));
    }

    // ── AJAX: upload a single image ───────────────────────────────────────────
    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            return response()->json(['message' => 'No valid file received.'], 422);
        }

        $path = $request->file('image')->store('products', 'public');

        return response()->json([
            'path' => $path,
            'url'  => Storage::disk('public')->url($path),
        ]);
    }

    // ── AJAX: delete a single image ───────────────────────────────────────────
    public function deleteImage(Request $request)
    {
        $path = $request->input('path', '');

        // Only allow deleting files inside the products folder
        if ($path && str_starts_with($path, 'products/')) {
            Storage::disk('public')->delete($path);
        }

        return response()->json(['ok' => true]);
    }

    // ── AJAX: upload a single video ───────────────────────────────────────────
    public function uploadVideo(Request $request)
    {
        if (!$request->hasFile('video') || !$request->file('video')->isValid()) {
            return response()->json(['message' => 'No valid file received.'], 422);
        }

        $path = $request->file('video')->store('products/videos', 'public');

        return response()->json([
            'path' => $path,
            'url'  => Storage::disk('public')->url($path),
        ]);
    }

    // ── AJAX: delete a single video ───────────────────────────────────────────
    public function deleteVideo(Request $request)
    {
        $path = $request->input('path', '');

        // Only allow deleting files inside the products/videos folder
        if ($path && str_starts_with($path, 'products/videos/')) {
            Storage::disk('public')->delete($path);
        }

        return response()->json(['ok' => true]);
    }

    // ── Index ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $products       = $this->buildQuery($request)->paginate(15)->withQueryString();
        $editingProduct = null;
        if ($request->filled('edit')) {
            $editingProduct = Product::findOrFail($request->edit);
        }
        return view('admin.products.index', compact('products', 'editingProduct'));
    }

    // ── AJAX filter ──────────────────────────────────────────────────────────
    public function filter(Request $request)
    {
        $page     = max(1, (int) $request->input('page', 1));
        $products = $this->buildQuery($request)->paginate(15, ['*'], 'page', $page);

        return response()->json([
            'html'        => view('admin.products._table', compact('products'))->render(),
            'total'       => $products->total(),
            'currentPage' => $products->currentPage(),
            'lastPage'    => $products->lastPage(),
        ]);
    }

    // ── Edit data (AJAX GET) ──────────────────────────────────────────────────
    public function editData(Product $product)
    {
        $data                  = $product->toArray();
        $data['bullet_points'] = implode("\n", $product->bullet_points ?? []);
        $images                = $product->image ?? [];
        $data['image_paths']   = $images;
        $data['image_urls']    = array_map(
            fn($p) => Storage::disk('public')->url($p),
            $images
        );
        $videos               = $product->video ?? [];
        $data['video_paths']  = $videos;
        $data['video_urls']   = array_map(
            fn($p) => Storage::disk('public')->url($p),
            $videos
        );
        return response()->json($data);
    }

    // ── Store ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate($this->rules());
        Product::create($this->prepare($request));

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    // ── Update ────────────────────────────────────────────────────────────────
    public function update(Request $request, Product $product)
    {
        $request->validate($this->rules($product->id));
        $product->update($this->prepare($request));

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────
    public function destroy(Product $product)
    {
        foreach ($product->image ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }
        foreach ($product->video ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    // ── Product detail page ─────────────────────────────────────────────────
    public function show(Product $product)
    {
        if (!$product->is_visible || $product->status !== 'Active') {
            abort(404);
        }

        $product->load([
            'ingredient.subIngredients',
            'reviews' => function ($query) {
                $query->visible()->latest()->take(6);
            },
        ]);

        if ($product->reviews->isNotEmpty()) {
            $product->review_count = $product->reviews->count();
            $product->rating       = round($product->reviews->avg('stars'), 1);
        }

        return view('product.show', compact('product'));
    }

    public function submitReview(Request $request, Product $product)
    {
        if (!$product->is_visible || $product->status !== 'Active') {
            abort(404);
        }

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'title' => 'nullable|string|max:255',
            'body'  => 'required|string|min:20',
            'stars' => 'required|integer|between:1,5',
        ]);

        $review = Review::create(array_merge($data, [
            'product_id' => $product->id,
            'is_visible' => true,
        ]));

        $stats = $product->reviews()->visible()->get();
        $product->review_count = $stats->count();
        $product->rating       = $stats->avg('stars') ? round($stats->avg('stars'), 1) : 0;
        $product->save();

        return redirect()->route('products.show', $product)
            ->with('success', 'Thank you! Your review has been submitted.');
    }

    // ── Shared query builder ──────────────────────────────────────────────────
    private function buildQuery(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(
                fn($q) =>
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('sku',  'like', "%{$s}%")
            );
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->boolean('featured')) {
            $query->where('featured', true);
        }

        return $query->latest();
    }
}
