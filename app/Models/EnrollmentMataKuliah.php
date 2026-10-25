<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentMataKuliah extends Model
{
    use HasFactory;

    protected $table = 'enrollment_mata_kuliah';

    protected $fillable = [
        'tahun_akademik_id',
        'mata_kuliah_id',
        'dosen_id',
        'evidence',
        'mata_kuliah_angkatan_kelas_id',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}
