<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAkademik extends Model
{
    use HasFactory;

    protected $table = 'tahun_akademik';

    protected $fillable = [
        'tahun',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function nilaiMahasiswa(): HasMany
    {
        return $this->hasMany(NilaiMahasiswa::class, 'tahun_akademik_id');
    }

    public function angkatanKelas(): HasMany
    {
        return $this->hasMany(AngkatanKelas::class, 'tahun_akademik_id');
    }

    public function enrollmentMataKuliah(): HasMany
    {
        return $this->hasMany(EnrollmentMataKuliah::class, 'tahun_akademik_id');
    }

    public function kegiatanMahasiswa(): HasMany
    {
        return $this->hasMany(KegiatanMahasiswa::class, 'tahun_akademik_id');
    }

    public function getNamaTahunAkademikAttribute(): string
    {
        return "{$this->tahun} {$this->semester}";
    }
}
