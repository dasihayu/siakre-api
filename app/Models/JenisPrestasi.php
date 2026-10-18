<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPrestasi extends Model
{
    use HasFactory;

    protected $table = 'jenis_prestasi';

    protected $fillable = [
        'id_prestasi_mahasiswa',
        'jenis',
    ];

    // Relasi ke Prestasi Mahasiswa
    public function prestasiMahasiswa()
    {
        return $this->belongsTo(PrestasiMahasiswa::class, 'id_prestasi_mahasiswa');
    }
}
