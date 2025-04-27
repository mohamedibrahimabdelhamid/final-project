<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\UserLibraryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // Store a new purchase or rental
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'type' => 'required|in:purchase,rent',
        ]);

        $authUserId = auth()->id(); // Logged-in user ID
        $type = $validated['type'];
        $date = Carbon::now();
        $expiryDate = $type === 'rent' ? $date->copy()->addDays(30) : null;

        $purchase = Purchase::create([
            'user_id' => $authUserId,
            'book_id' => $validated['book_id'],
            'type' => $type,
            'date' => $date,
            'expiry_date' => $expiryDate,
        ]);

        Transaction::create([
            'user_id' => $authUserId,
            'amount' => $purchase->book->price,
            'payment_method' => 'Credit Card',
            'status' => 'Completed',
            'date' => now(),
        ]);

        UserLibraryItem::create([
            'user_id' => $authUserId,
            'book_id' => $validated['book_id'],
            'access_type' => $type,
        ]);

        return response()->json([
            'message' => 'Purchase saved successfully',
            'purchase' => $purchase
        ], 201);
    }

    // Get the logged-in user's library
    public function getUserLibrary()
    {
        $userId = auth()->id();

        $library = UserLibraryItem::where('user_id', $userId)
            ->with('book','book.audiobook', 'book.tags')
            ->get();
        return response()->json($library);
    }

    // Show a single purchase
    public function show($id)
    {
        $purchase = Purchase::with(['user', 'book'])->findOrFail($id);

        if ($purchase->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized to view this purchase'], 403);
        }

        return response()->json($purchase);
    }

    // Delete a purchase
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized to delete this purchase'], 403);
        }

        $purchase->delete();

        return response()->json(['message' => 'Purchase/rental deleted successfully']);
    }
}
