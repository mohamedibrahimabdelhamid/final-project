<?php

namespace Database\Seeders;

use App\Models\Audiobook;
use App\Models\Book;
use Illuminate\Database\Seeder;

class AudiobookSeeder extends Seeder
{
    public function run()
    {
        $books = Book::take(10)->get(); // Take first 10 books

        foreach ($books as $book) {
            Audiobook::create([
                'book_id' => $book->id,
                'file_url' => 'audiobooks/sample.mp3'
            ]);
        }
    }
}
