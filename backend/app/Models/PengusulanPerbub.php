<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengusulanPerbub extends Model
{
    use HasFactory;

    protected $table = 'pengusulan_perbub';

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'dinas_id',
        'kategori_dokumen_id',
        'judul_perbub',
        'latar_belakang',
        'maksud_dan_tujuan',
        'ruang_lingkup',
        'status',
        'catatan',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function dinas()
    {
        return $this->belongsTo(User::class, 'dinas_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenPerbub::class, 'pengusulan_id');
    }

    public function catatanRevisi()
    {
        return $this->hasMany(CatatanRevisi::class, 'pengusulan_id');
    }

    public function kategoriDokumen()
    {
        return $this->belongsTo(KategoriDokumen::class, 'kategori_dokumen_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(StatusHistory::class, 'pengusulan_id')->orderBy('created_at', 'desc');
    }
}

