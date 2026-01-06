<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanRevisi extends Model
{
    use HasFactory;

    protected $table = 'catatan_revisi';

    protected $fillable = [
        'pengusulan_id',
        'created_by',
        'tipe',
        'catatan',
        'file_path',
        'file_name',
        'is_resolved',
        'resolved_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function pengusulan()
    {
        return $this->belongsTo(PengusulanPerbub::class, 'pengusulan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
