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

    // public function usersInCart()
    // {
    //     return $this->belongsToMany(User::class, 'cart_items');
    // }

    // public function usersInLibrary()
    // {
    //     return $this->belongsToMany(User::class, 'user_library_items');
    // }


    // public function reviews()
    // {
    //     return $this->hasMany(Review::class);
    // }

    // public function discount()
    // {
    //     return $this->hasOne(Discount::class);
    // }
}
