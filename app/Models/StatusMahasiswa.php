<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'status_mahasiswa';

    protected $fillable = [
        'id_enrollment_mahasiswa_angkatan_kelas',
        'status',
    ];

    public function enrollment()
    {
        return $this->belongsTo(EnrollmentKelas::class, 'id_enrollment_mahasiswa_angkatan_kelas');
    }
}