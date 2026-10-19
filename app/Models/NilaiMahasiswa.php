<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function enrollmentKelas(): BelongsTo
    {
        return $this->belongsTo(EnrollmentKelas::class, 'enrollment_mahasiswa_angkatan_kelas_id');
    }

    public function mataKuliahAngkatanKelas(): BelongsTo
    {
        return $this->belongsTo(MataKuliahAngkatanKelas::class, 'mata_kuliah_angkatan_kelas_id');
    }
}
