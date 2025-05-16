<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run()
    {
        $genres = ['Fiction', 'Science', 'Mystery', 'Romance', 'Horror', 'Adventure', 'Biography'];
        $availabilities = ['Free', 'Purchase']; // No 'Rent'

        for ($i = 1; $i <= 10; $i++) {
            Book::create([
                'title' => 'The Tale of ' . Str::random(5),
                'author' => 'Author ' . Str::random(6),
                'genre' => $genres[array_rand($genres)],
                'description' => 'This is a fascinating story about ' . Str::random(15),
                'price' => rand(5, 100),
                'availability' => $availabilities[array_rand($availabilities)],
                'published_date' => now()->subDays(rand(1, 1000)),
                'file_url' => 'ebooks/1CBXf2UF8u2Po8oIKA9uemeqju0kQloTRiAmmahh.pdf',
                'image' => 'book_images/sample' . $i . '.jpg',
                'text_sample' => 'samples/text/sample.txt',
                'audio_sample' => 'samples/audio/sample.mp3',
            ]);
        }
    }
}
