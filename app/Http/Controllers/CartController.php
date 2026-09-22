<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();
        $cartItems = $user->cart_data ?? [];
        return view('cart.index', ['cartItems' => $cartItems]);
    }
}
