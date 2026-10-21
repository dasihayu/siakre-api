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
        'mata_kuliah_angkatan_kelas_id',
        'link_nilai',
        'link_evidence',
    ];

    public function mataKuliahAngkatanKelas(): BelongsTo
    {
        return $this->belongsTo(MataKuliahAngkatanKelas::class, 'mata_kuliah_angkatan_kelas_id');
    }
}
