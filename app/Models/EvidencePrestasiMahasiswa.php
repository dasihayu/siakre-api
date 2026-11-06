<?php

namespace App\Models;

use App\Enums\JenisEvidencePrestasi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidencePrestasiMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'evidence_prestasi_mahasiswa';

    protected $fillable = [
        'id_prestasi_mahasiswa',
        'jenis_evidence',
        'judul',
        'url',
        'evidence',
    ];

    protected $appends = [
        'evidence',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jenis_evidence' => JenisEvidencePrestasi::class,
        ];
    }

    /**
     * Relasi ke Prestasi Mahasiswa
     */
    public function prestasiMahasiswa(): BelongsTo
    {
        return $this->belongsTo(PrestasiMahasiswa::class, 'id_prestasi_mahasiswa');
    }

    /**
     * Accessor untuk kompatibilitas backward atribut 'evidence'.
     */
    public function getEvidenceAttribute(): ?string
    {
        return $this->attributes['url'] ?? null;
    }

    /**
     * Mutator untuk kompatibilitas backward atribut 'evidence'.
     */
    public function setEvidenceAttribute(?string $value): void
    {
        $this->attributes['url'] = $value;
    }
}
