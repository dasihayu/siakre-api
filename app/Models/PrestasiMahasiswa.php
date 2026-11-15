<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrestasiMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'prestasi_mahasiswa';

    protected $fillable = [
        'id_mahasiswa',
        'id_jenis_prestasi',
        'nama_aktivitas',
        'tahun',
    ];

    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    /**
     * Relasi ke Jenis Prestasi (Prestasi Mahasiswa merujuk ke Master Jenis Prestasi)
     */
    public function jenisPrestasi(): BelongsTo
    {
        return $this->belongsTo(JenisPrestasi::class, 'id_jenis_prestasi');
    }

    /**
     * Relasi ke Evidence Prestasi Mahasiswa
     */
    public function evidence(): HasMany
    {
        return $this->hasMany(EvidencePrestasiMahasiswa::class, 'id_prestasi_mahasiswa');
    }
}