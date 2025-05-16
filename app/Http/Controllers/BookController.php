<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function __construct()
    {

        // $this->middleware('auth:api');
    }
    public function index()
    {
        return Book::with(['audiobook', 'tags'])->get();
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'genre' => 'required|string',
            'description' => 'sometimes|nullable|string',
            'price' => 'required|numeric',
            'availability' => 'required|in:Free,Purchase,Rent',
            'published_date' => 'required|date',
            'file' => 'required|file|mimes:pdf,epub',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'text_sample' => 'nullable|file|mimes:pdf,txt',
            'audio_sample' => 'nullable|file|mimes:mp3,wav',

        ]);

        $fileUrl = $request->file('file')->store('ebooks', 'public');
        $imageUrl = $request->hasFile('image') ? $request->file('image')->store('book_images', 'public') : null;
        $textSampleUrl = $request->hasFile('text_sample') ? $request->file('text_sample')->store('samples/text', 'public') : null;
        $audioSampleUrl = $request->hasFile('audio_sample') ? $request->file('audio_sample')->store('samples/audio', 'public') : null;


        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'genre' => $request->genre,
            'description' => $request->description,
            'price' => $request->price,
            'availability' => $request->availability,
            'published_date' => $request->published_date,
            'file_url' => $fileUrl,
            'image' => $imageUrl,
            'text_sample' => $textSampleUrl,
            'audio_sample' => $audioSampleUrl,


        ]);

        return response()->json([
            'message' => 'Book added successfully',
            'book' => $book
        ], 201);
    }

    public function show($id)
    {
        return Book::with(['audiobook', 'tags'])->findOrFail($id);
    }


    public function getByGenre($genre)
    {
        $books = Book::where('genre', $genre)->get();
        return response()->json($books);
    }

    public function browse(Request $request)
    {
        $query = Book::query();

        if ($request->has('genre')) {
            $genres = explode(',', $request->genre);
            $query->whereIn('genre', $genres);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('author', 'like', "%$search%");
            });
        }
        if ($request->has('tag')) {
            $tags = explode(',', $request->tag);
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('tag_name', $tags);
            });
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'popularity':
                    $query->withCount('libraryItems')->orderByDesc('library_items_count');
                    break;
                case 'new':
                    $query->orderByDesc('published_date');
                    break;
                case 'alphabetical':
                    $query->orderBy('title');
                    break;
            }
        }

        return response()->json($query->with('tags')->get());
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string',
            'author' => 'sometimes|required|string',
            'genre' => 'sometimes|required|string',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric',
            'availability' => 'sometimes|required|in:Free,Purchase,Rent',
            'published_date' => 'sometimes|required|date',
            'file' => 'nullable|file|mimes:pdf,epub',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'text_sample' => 'nullable|file|mimes:pdf,txt',
            'audio_sample' => 'nullable|file|mimes:mp3,wav',

        ]);

        if ($request->hasFile('file')) {
            if ($book->file_url && Storage::disk('public')->exists($book->file_url)) {
                Storage::disk('public')->delete($book->file_url);
            }
            $book->file_url = $request->file('file')->store('ebooks', 'public');
        }

        if ($request->hasFile('image')) {
            if ($book->image && Storage::disk('public')->exists($book->image)) {
                Storage::disk('public')->delete($book->image);
            }
            $book->image = $request->file('image')->store('book_images', 'public');
        }
        if ($request->hasFile('text_sample')) {
            if ($book->text_sample && Storage::disk('public')->exists($book->text_sample)) {
                Storage::disk('public')->delete($book->text_sample);
            }
            $book->text_sample = $request->file('text_sample')->store('samples/text', 'public');
        }

        if ($request->hasFile('audio_sample')) {
            if ($book->audio_sample && Storage::disk('public')->exists($book->audio_sample)) {
                Storage::disk('public')->delete($book->audio_sample);
            }
            $book->audio_sample = $request->file('audio_sample')->store('samples/audio', 'public');
        }


        $book->update($request->only([
            'title', 'author', 'genre','description' , 'price', 'availability', 'published_date'
        ]));

        return response()->json($book);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $book = Book::findOrFail($id);

        // Optional: Delete files from storage
        if ($book->file_url && Storage::disk('public')->exists($book->file_url)) {
            Storage::disk('public')->delete($book->file_url);
        }
        if ($book->image && Storage::disk('public')->exists($book->image)) {
            Storage::disk('public')->delete($book->image);
        }
        if ($book->text_sample && Storage::disk('public')->exists($book->text_sample)) {
            Storage::disk('public')->delete($book->text_sample);
        }
        if ($book->audio_sample && Storage::disk('public')->exists($book->audio_sample)) {
            Storage::disk('public')->delete($book->audio_sample);
        }

        $book->delete();

        return response()->json(['message' => 'Book deleted']);
    }
}
