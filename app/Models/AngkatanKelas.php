<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AngkatanKelas extends Model
{
    use HasFactory;

    protected $table = 'angkatan_kelas';

    protected $fillable = [
        'kelas_id',
        'angkatan_id',
        'prodi_id',
        'tahun_akademik_id',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function angkatan()
    {
        return $this->belongsTo(Angkatan::class, 'angkatan_id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function enrollmentKelas()
    {
        return $this->hasMany(EnrollmentKelas::class, 'angkatan_kelas_id');
    }

    public function mataKuliahAngkatanKelas()
    {
        return $this->hasMany(MataKuliahAngkatanKelas::class, 'angkatan_kelas_id');
    }

    public function mahasiswa()
    {
        return $this->belongsToMany(
            Mahasiswa::class,
            'enrollment_mahasiswa_angkatan_kelas',
            'angkatan_kelas_id',
            'id_mahasiswa'
        )->withPivot('id', 'prodi_id')->withTimestamps();
    }
}
