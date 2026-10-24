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
        'mahasiswa_id',
        'semester',
        'nilai_angka',
        'nilai_huruf',
        'bobot',
        'link_nilai',
        'link_evidence',
    ];

    protected $casts = [
        'semester' => 'integer',
        'nilai_angka' => 'float',
        'bobot' => 'float',
    ];

    public function mataKuliahAngkatanKelas(): BelongsTo
    {
        return $this->belongsTo(MataKuliahAngkatanKelas::class, 'mata_kuliah_angkatan_kelas_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Helper untuk menghitung nilai huruf dan bobot berdasarkan nilai angka.
     *
     * @return array{nilai_huruf: string, bobot: float}
     */
    public static function hitungNilaiHurufDanBobot(float $nilaiAngka): array
    {
        return match (true) {
            $nilaiAngka >= 80 => ['nilai_huruf' => 'A', 'bobot' => 4.00],
            $nilaiAngka >= 75 => ['nilai_huruf' => 'AB', 'bobot' => 3.50],
            $nilaiAngka >= 65 => ['nilai_huruf' => 'B', 'bobot' => 3.00],
            $nilaiAngka >= 60 => ['nilai_huruf' => 'BC', 'bobot' => 2.50],
            $nilaiAngka >= 50 => ['nilai_huruf' => 'C', 'bobot' => 2.00],
            $nilaiAngka >= 40 => ['nilai_huruf' => 'D', 'bobot' => 1.00],
            default => ['nilai_huruf' => 'E', 'bobot' => 0.00],
        };
    }
}
