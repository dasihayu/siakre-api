<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestasiMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'prestasi_mahasiswa';

    protected $fillable = [
        'id_mahasiswa',
        'nama_aktivitas',
        'tahun',
    ];

    // Relasi ke Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    // Relasi ke Evidence Prestasi Mahasiswa
    public function evidence()
    {
        return $this->hasMany(EvidencePrestasiMahasiswa::class, 'id_prestasi_mahasiswa');
    }

    // Relasi ke Jenis Prestasi
    public function jenisPrestasi()
    {
        return $this->hasMany(JenisPrestasi::class, 'id_prestasi_mahasiswa');
    }
}
