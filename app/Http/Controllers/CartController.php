<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;


class CartController extends Controller
{
    // List cart items for a specific user
    public function allCarts()
    {
        $cartItems = CartItem::with(['user', 'book'])->get();

        return response()->json($cartItems);
    }

    public function index()
    {
        $userId = auth()->id();
        $cartItems = CartItem::with('book')
            ->where('user_id', $userId)
            ->get();

        return response()->json($cartItems);
    }


    // Add item to cart
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $userId = auth()->id(); // Get the logged-in user's id

        $existing = CartItem::where('user_id', $userId)
            ->where('book_id', $validated['book_id'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Book already in cart'], 409);
        }

        $cartItem = CartItem::create([
            'user_id' => $userId,           
            'book_id' => $validated['book_id'],
        ]);

        return response()->json($cartItem, 201);
    }


    // Remove item from cart
    public function destroy($id)
    {
        $cartItem = CartItem::find($id);

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Cart item removed']);
    }

}
