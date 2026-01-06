<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'slug',
        'isi',
        'gambar',
        'penulis',
        'is_published',
        'is_headline',
        'published_at',
        'views',
        'order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_headline' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer',
        'order' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}

