<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'author', 'image', 'genre', 'description' , 'price', 'availability', 'published_date', 'file_url', 'text_sample', 'audio_sample'
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
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }   

}
