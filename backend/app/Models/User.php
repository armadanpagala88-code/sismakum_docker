<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nip',
        'jabatan',
        'email',
        'password',
        'role',
        'unit_kerja',
        'dinas_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function pengusulanPerbub()
    {
        return $this->hasMany(PengusulanPerbub::class, 'dinas_id');
    }

    public function reviews()
    {
        return $this->hasMany(PengusulanPerbub::class, 'reviewed_by');
    }

    public function dinas()
    {
        return $this->belongsTo(Dinas::class);
    }
}
