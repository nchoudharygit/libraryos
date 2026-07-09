<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'author_id',
        'title',
        'isbn',
        'genre',
        'description',
        'total_copies',
        'published_date'
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
