<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            // 'audiobook_id' => 'nullable|exists:audiobooks,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $review = Review::create(array_merge($validated, [
            'date' => now(),
        ]));

        return response()->json([
            'message' => 'Review submitted successfully.',
            'review' => $review
        ], 201);
    }

    // Get all reviews for a book
    public function bookReviews($bookId)
    {
        return Review::where('book_id', $bookId)->with('user')->get();
    }
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        return response()->json([
            'message' => 'Review deleted successfully.'
        ]);
    }
}

