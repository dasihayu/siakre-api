<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'data_mahasiswa';

    protected $fillable = [
        'nim',
        'nama_mahasiswa',
        'tahun_masuk',
        'jenis_pendaftaran',
        'mahasiswa_internasional'
    ];
}