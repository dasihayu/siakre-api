<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'data_mahasiswa';

    protected $fillable = [
        'nim',
        'nama_mahasiswa',
        'tahun_masuk',
        'jenis_pendaftaran',
        'tanggal_kelulusan',
        'masa_studi_bulan',
    ];

    public function nilaiMahasiswa(): HasMany
    {
        return $this->hasMany(NilaiMahasiswa::class, 'mahasiswa_id');
    }
}