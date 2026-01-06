<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $table = 'website';

    protected $fillable = [
        'key',
        'title',
        'content',
        'type',
        'is_active',
        'order',
        'image_url',
        'file_path',
        'bupati_photo',
        'wakil_bupati_photo',
        'sekda_photo',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

