<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api'); // Apply auth middleware
    }
    public function store(Request $request)
    {
        $validated = $request->validate([

            'book_id' => 'required|exists:books,id',
            // 'audiobook_id' => 'nullable|exists:audiobooks,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);
        $userId = auth()->id();

        $review = Review::create([
            'user_id' => $userId,
            'book_id' => $validated['book_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'date' => now(),
        ]);

        return response()->json([
            'message' => 'Review submitted successfully.',
            'review' => $review
        ], 201);
    }

    // Get all reviews for a book
    public function bookReviews($bookId)
    {
        return Review::where('book_id', $bookId)
            ->with('user:id,name') // load user's name with review
            ->get();
    }

    // Delete a review (only owner can delete)
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'You are not authorized to delete this review.'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully.'
        ]);
    }
}

