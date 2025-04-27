<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');
    }
    public function index()
    {
        return Discount::with('book')->get();
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'discount_type' => 'required|in:Fixed,Percentage',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $discount = Discount::create($request->all());

        return response()->json([
            'message' => 'Discount created successfully',
            'discount' => $discount
        ], 201);
    }

    public function show($id)
    {
        return Discount::with('book')->findOrFail($id);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        Discount::destroy($id);
        return response()->json(['message' => 'Discount deleted']);
    }
}

