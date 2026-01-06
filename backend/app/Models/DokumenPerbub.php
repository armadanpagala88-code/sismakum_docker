<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenPerbub extends Model
{
    use HasFactory;

    protected $table = 'dokumen_perbub';

    protected $fillable = [
        'pengusulan_id',
        'nama_file',
        'path_file',
        'tipe_dokumen',
        'ukuran_file',
    ];

    public function pengusulan()
    {
        return $this->belongsTo(PengusulanPerbub::class, 'pengusulan_id');
    }
}

