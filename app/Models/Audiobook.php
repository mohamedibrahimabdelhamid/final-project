<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audiobook extends Model
{
    use HasFactory;
    protected $fillable = [
        'book_id',
        'file_url'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
