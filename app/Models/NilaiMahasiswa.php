<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'nilai_mahasiswa';

    protected $fillable = [
        'enrollment_mahasiswa_angkatan_kelas_id',
        'mata_kuliah_angkatan_kelas_id',
        'nilai_angka',
        'nilai_huruf',
        'bobot_nilai',
        'status_kelulusan',
    ];
}