<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'cart' => 'present|array',
            'cart.*.name' => 'required|string|max:255',
            'cart.*.price' => 'required|numeric|min:0',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.img' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        // save cart data to user's cart_data JSON field (empty array clears the cart)
        $user->cart_data = $request->input('cart', []);
        $user->save();

        return response()->json(['ok' => true]);
    }

    /**
     * Display the authenticated user's cart items.
     */
    public function index(Request $request)
    {
        $user      = Auth::user();
        $cartItems = $user->cart_data ?? [];

        // Repair product images using actual storage URLs
        $productIds = array_filter(array_column($cartItems, 'product_id'));
        if (!empty($productIds)) {
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
            $cartItems = array_map(function ($item) use ($products) {
                $id = $item['product_id'] ?? null;
                if ($id && isset($products[$id])) {
                    $images = $products[$id]->image ?? [];
                    if (!empty($images[0])) {
                        $item['img'] = Storage::disk('public')->url($images[0]);
                    }
                }
                return $item;
            }, $cartItems);
        }

        return view('cart.index', ['cartItems' => $cartItems]);
    }
}
