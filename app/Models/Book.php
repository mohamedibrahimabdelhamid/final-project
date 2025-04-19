<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'author', 'image', 'genre', 'price', 'availability', 'published_date', 'file_url'
    ];

    // Relationships
    public function audiobook()
    {
        return $this->hasOne(Audiobook::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
