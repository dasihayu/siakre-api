<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidencePrestasiMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'evidence_prestasi_mahasiswa';

    protected $fillable = [
        'id_prestasi_mahasiswa',
        'evidence',
    ];

    // Relasi ke Prestasi Mahasiswa
    public function prestasiMahasiswa()
    {
        return $this->belongsTo(PrestasiMahasiswa::class, 'id_prestasi_mahasiswa');
    }
}
