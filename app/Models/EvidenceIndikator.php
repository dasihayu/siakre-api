<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidenceIndikator extends Model
{
    use HasFactory;

    protected $table = 'evidence_indikator';

    protected $fillable = [
        'tipe_sumber',
        'id_sumber',
        'id_indikator_lam',
    ];

    protected $casts = [
        'id_sumber' => 'integer',
    ];

    public function indikatorLam(): BelongsTo
    {
        return $this->belongsTo(IndikatorLam::class, 'id_indikator_lam');
    }

    /**
     * Mengembalikan sumber evidence secara polimorfik berdasarkan tipe_sumber.
     */
    public function sumber(): ?Model
    {
        return match ($this->tipe_sumber) {
            'nilai_mahasiswa' => NilaiMahasiswa::with([
                'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
                'mahasiswa',
                'tahunAkademik',
            ])->find($this->id_sumber),
            'prestasi_mahasiswa' => PrestasiMahasiswa::with(['mahasiswa', 'evidence', 'jenisPrestasi'])->find($this->id_sumber),
            'kegiatan_mahasiswa' => KegiatanMahasiswa::with('mahasiswa')->find($this->id_sumber),
            default => null,
        };
    }
}
