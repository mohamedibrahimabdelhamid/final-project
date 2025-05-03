<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function serve($type, $filename)
    {
        if (!in_array($type, ['ebooks', 'audiobooks'])) {
            return response()->json(['message' => 'Invalid type'], 400);
        }

        $path = $type . '/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->file(Storage::disk('public')->path($path));
    }

}
