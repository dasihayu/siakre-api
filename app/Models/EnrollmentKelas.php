<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentKelas extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel secara eksplisit sesuai ERD
    protected $table = 'enrollment_mahasiswa_angkatan_kelas';

    protected $fillable = [
        'id_mahasiswa',
        'angkatan_kelas_id',
        'prodi_id',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    public function angkatanKelas()
    {
        return $this->belongsTo(AngkatanKelas::class, 'angkatan_kelas_id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}
