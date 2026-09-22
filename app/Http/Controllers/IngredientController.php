<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\SubIngredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    // ── Index (full page) ──────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $products    = Product::orderBy('name')->get(['id', 'name']);
        $ingredients = $this->buildQuery($request)->paginate(15)->withQueryString();

        $editingIngredient = null;
        if ($request->filled('edit')) {
            $editingIngredient = Ingredient::with('subIngredients')->findOrFail($request->edit);
        }

        return view('admin.ingredients.index', compact('products', 'ingredients', 'editingIngredient'));
    }

    // ── AJAX filter (POST) ────────────────────────────────────────────────────
    public function filter(Request $request)
    {
        $page        = max(1, (int) $request->input('page', 1));
        $ingredients = $this->buildQuery($request)->paginate(15, ['*'], 'page', $page);

        return response()->json([
            'html'        => view('admin.ingredients._table', compact('ingredients'))->render(),
            'total'       => $ingredients->total(),
            'currentPage' => $ingredients->currentPage(),
            'lastPage'    => $ingredients->lastPage(),
        ]);
    }

    // ── Edit data (AJAX GET) ──────────────────────────────────────────────────
    public function editData(Ingredient $ingredient)
    {
        return response()->json($ingredient->load('subIngredients'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'product_id'                => 'required|exists:products,id',
            'name'                      => 'required|string|max:255',
            'amount'                    => 'nullable|string|max:100',
            'description'               => 'nullable|string',
            'sub.*.name'                => 'required|string|max:255',
            'sub.*.amount'              => 'nullable|string|max:100',
            'sub.*.unit'                => 'nullable|string|max:50',
            'sub.*.description'         => 'nullable|string',
        ]);

        // Only one ingredient per product
        if (Ingredient::where('product_id', $request->product_id)->exists()) {
            return back()->withErrors(['product_id' => 'This product already has an ingredient entry. Edit or delete it first.'])->withInput();
        }

        DB::transaction(function () use ($request) {
            $ingredient = Ingredient::create([
                'product_id'  => $request->product_id,
                'name'        => $request->name,
                'amount'      => $request->amount,
                'description' => $request->description,
            ]);

            foreach ((array) $request->sub as $i => $row) {
                if (empty($row['name'])) continue;
                SubIngredient::create([
                    'ingredient_id' => $ingredient->id,
                    'name'          => $row['name'],
                    'amount'        => $row['amount']       ?? null,
                    'unit'          => $row['unit']         ?? null,
                    'description'   => $row['description']  ?? null,
                    'sort_order'    => $i,
                ]);
            }
        });

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingredient created successfully.');
    }

    // ── Update ────────────────────────────────────────────────────────────────
    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'product_id'                => 'required|exists:products,id',
            'name'                      => 'required|string|max:255',
            'amount'                    => 'nullable|string|max:100',
            'description'               => 'nullable|string',
            'sub.*.name'                => 'required|string|max:255',
            'sub.*.amount'              => 'nullable|string|max:100',
            'sub.*.unit'                => 'nullable|string|max:50',
            'sub.*.description'         => 'nullable|string',
        ]);

        // Guard: if product changed, make sure target product has no other ingredient
        if (
            (int) $request->product_id !== $ingredient->product_id &&
            Ingredient::where('product_id', $request->product_id)->exists()
        ) {
            return back()->withErrors(['product_id' => 'That product already has an ingredient entry.'])->withInput();
        }

        DB::transaction(function () use ($request, $ingredient) {
            $ingredient->update([
                'product_id'  => $request->product_id,
                'name'        => $request->name,
                'amount'      => $request->amount,
                'description' => $request->description,
            ]);

            // Replace sub-ingredients entirely
            $ingredient->subIngredients()->delete();
            foreach ((array) $request->sub as $i => $row) {
                if (empty($row['name'])) continue;
                SubIngredient::create([
                    'ingredient_id' => $ingredient->id,
                    'name'          => $row['name'],
                    'amount'        => $row['amount']       ?? null,
                    'unit'          => $row['unit']         ?? null,
                    'description'   => $row['description']  ?? null,
                    'sort_order'    => $i,
                ]);
            }
        });

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingredient updated successfully.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete(); // cascades to sub_ingredients via FK

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingredient deleted successfully.');
    }

    // ── Shared query builder ──────────────────────────────────────────────────
    private function buildQuery(Request $request)
    {
        $query = Ingredient::with(['product:id,name,image', 'subIngredients'])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('product_id') && $request->product_id !== 'all') {
            $query->where('product_id', $request->product_id);
        }

        return $query;
    }
}
