<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        return Book::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'genre' => 'required|string',
            'price' => 'required|numeric',
            'availability' => 'required|in:Free,Purchase,Rent',
            'published_date' => 'required|date',
            'file' => 'required|file|mimes:pdf,epub',
            'image' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        // Save files
        $fileUrl = $request->file('file')->store('ebooks', 'public');
        $imageUrl = $request->hasFile('image') ? $request->file('image')->store('book_images', 'public') : null;

        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'genre' => $request->genre,
            'price' => $request->price,
            'availability' => $request->availability,
            'published_date' => $request->published_date,
            'file_url' => $fileUrl,
            'image' => $imageUrl
        ]);

        return response()->json([
            'message' => 'Book added successfully',
            'book' => $book
        ], 201);
    }

    public function show($id)
    {
        return Book::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string',
            'author' => 'sometimes|required|string',
            'genre' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'availability' => 'sometimes|required|in:Free,Purchase,Rent',
            'published_date' => 'sometimes|required|date',
            'file' => 'nullable|file|mimes:pdf,epub',
            'image' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

          // Delete and replace file if new one uploaded
                // if ($request->hasFile('file')) {
                //     if ($book->file_url && Storage::disk('public')->exists($book->file_url)) {
                //         Storage::disk('public')->delete($book->file_url);
                //     }
                //     $book->file_url = $request->file('file')->store('ebooks', 'public');
                // }

                // // Delete and replace image if new one uploaded
                // if ($request->hasFile('image')) {
                //     if ($book->image && Storage::disk('public')->exists($book->image)) {
                //         Storage::disk('public')->delete($book->image);
                //     }
                //     $book->image = $request->file('image')->store('book_images', 'public');
            // }

        if ($request->hasFile('file')) {
            $book->file_url = $request->file('file')->store('ebooks', 'public');
        }

        if ($request->hasFile('image')) {
            $book->image = $request->file('image')->store('book_images', 'public');
        }

        $book->update($request->only([
            'title', 'author', 'genre', 'price', 'availability', 'published_date'
        ]));

        return response()->json($book);
    }

    public function destroy($id)
    {
        Book::destroy($id);
        return response()->json(['message' => 'Book deleted']);
    }
}
