<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\UserLibraryItem;

class PurchaseController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'type' => 'required|in:purchase,rent',
        ]);

        $type = $validated['type'];
        $date = Carbon::now();
        $expiryDate = $type === 'rent' ? $date->copy()->addDays(30) : null;



        $purchase = Purchase::create([
            'user_id' => $validated['user_id'],
            'book_id' => $validated['book_id'],
            'type' => $type,
            'date' => $date,
            'expiry_date' => $expiryDate,
        ]);
        Transaction::create([
            'user_id' => $validated['user_id'],
            'amount' => $purchase->book->price,
            'payment_method' => 'Credit Card',
            'status' => 'Completed',
            'date' => now(),
        ]);
        UserLibraryItem::create([
            'user_id' => $validated['user_id'],
            'book_id' => $validated['book_id'],
            'access_type' => $type
        ]);

        return response()->json([
            'message' => 'Purchase saved successfully',
            'purchase' => $purchase
        ], 201);
    }
    public function getUserLibrary($userId)
    {
        $library = UserLibraryItem::where('user_id', $userId)
            ->with('book')
            ->get();

        return response()->json($library);
    }

        public function show($id)
    {
        return Purchase::with(['user', 'book'])->findOrFail($id);
    }

        public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();

        return response()->json(['message' => 'Purchase/rental deleted successfully']);
    }
}
