<?php

namespace App\Http\Controllers;

use App\Models\Audiobook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AudiobookController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');
    }
    public function index()
    {
        return Audiobook::with('book')->get();
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
            'file' => 'required|file|mimes:mp3,wav|max:20480' // 20MB = 20480KB
        ], [
            'file.max' => 'The file must not exceed 20MB.',
            'file.mimes' => 'Only MP3 or WAV files are allowed.'
        ]);

        $filePath = $request->file('file')->store('audiobooks', 'public');

        $audiobook = Audiobook::create([
            'book_id' => $request->book_id,
            'file_url' => $filePath
        ]);

        return response()->json([
            'message' => 'Audiobook uploaded successfully',
            'audiobook' => $audiobook
        ], 201);
    }

    public function show($id)
    {
        return Audiobook::with('book')->findOrFail($id);
    }

    public function update(Request $request, $id)
{
    $user = auth()->user();
    if ($user->role !== 'Admin') {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $audiobook = Audiobook::findOrFail($id);

        $request->validate([
            'file' => 'nullable|file|mimes:mp3,wav|max:20480' // 20MB = 20480KB
        ], [
            'file.max' => 'The file must not exceed 20MB.',
            'file.mimes' => 'Only MP3 or WAV files are allowed.'
        ]);

        if ($request->hasFile('file')) {
            if ($audiobook->file_url && Storage::disk('public')->exists($audiobook->file_url)) {
                Storage::disk('public')->delete($audiobook->file_url);
            }
            $audiobook->file_url = $request->file('file')->store('audiobooks', 'public');
        }

        $audiobook->save();

        return response()->json($audiobook);
}

    public function destroy($id)
    {
        $user = auth()->user();
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $audiobook = Audiobook::findOrFail($id);

        if ($audiobook->file_url && Storage::disk('public')->exists($audiobook->file_url)) {
            Storage::disk('public')->delete($audiobook->file_url);
        }

        $audiobook->delete();

        return response()->json(['message' => 'Audiobook deleted successfully']);
    }
}
