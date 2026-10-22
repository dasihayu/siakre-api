<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliahAngkatanKelas extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah_angkatan_kelas';

    protected $fillable = [
        'enrollment_mata_kuliah_id',
        'angkatan_kelas_id',
    ];

    public function enrollmentMataKuliah(): BelongsTo
    {
        return $this->belongsTo(EnrollmentMataKuliah::class, 'enrollment_mata_kuliah_id');
    }

    public function angkatanKelas(): BelongsTo
    {
        return $this->belongsTo(AngkatanKelas::class, 'angkatan_kelas_id');
    }

    public function nilaiMahasiswa(): HasMany
    {
        return $this->hasMany(NilaiMahasiswa::class, 'mata_kuliah_angkatan_kelas_id');
    }
}
