<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Book;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'tag_name' => 'required|string|unique:tags,tag_name'
        ]);

        $tag = Tag::create(['tag_name' => $request->tag_name]);

        return response()->json(['message' => 'Tag created', 'tag' => $tag], 201);
    }

    public function show($id)
    {
        return Tag::with('books')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $request->validate([
            'tag_name' => 'required|string|unique:tags,tag_name,' . $id
        ]);
        $tag->update(['tag_name' => $request->tag_name]);

        return response()->json(['message' => 'Tag updated', 'tag' => $tag]);
    }

    public function destroy($id)
    {
        Tag::destroy($id);
        return response()->json(['message' => 'Tag deleted']);
    }

    // Attach tag to a book
    public function attachToBook(Request $request, $bookId)
    {
        $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:tags,id'
        ]);

        $book = Book::findOrFail($bookId);
        $book->tags()->sync($request->tag_ids); // Replace existing tags

        return response()->json(['message' => 'Tags attached to book']);
    }
}
