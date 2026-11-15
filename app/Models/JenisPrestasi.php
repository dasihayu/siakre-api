<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPrestasi extends Model
{
    use HasFactory;

    protected $table = 'jenis_prestasi';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'tingkat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Prestasi Mahasiswa (1 Jenis Prestasi bisa dimiliki banyak Prestasi Mahasiswa)
     */
    public function prestasiMahasiswa(): HasMany
    {
        return $this->hasMany(PrestasiMahasiswa::class, 'id_jenis_prestasi');
    }
}