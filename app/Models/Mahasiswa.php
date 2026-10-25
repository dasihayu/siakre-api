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

    protected $appends = [
        'nim_display',
    ];

    public function getNimDisplayAttribute(): string
    {
        $raw = (string) $this->nim;
        if (preg_match('/^(\d)(\d{2})(\d{2})(\d)(\d{2})$/', $raw, $m)) {
            return "{$m[1]}.{$m[2]}.{$m[3]}.{$m[4]}.{$m[5]}";
        }

        return $raw;
    }

    public function nilaiMahasiswa(): HasMany
    {
        return $this->hasMany(NilaiMahasiswa::class, 'mahasiswa_id');
    }
}
