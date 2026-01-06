<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infokami extends Model
{
    use HasFactory;

    protected $table = 'infokami';

    protected $fillable = [
        'nama_file',
        'file_pdf',
        'cover_pdf',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

