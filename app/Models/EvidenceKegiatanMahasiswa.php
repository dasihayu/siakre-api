<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidenceKegiatanMahasiswa extends Model
{
    protected $table = 'evidence_kegiatan_mahasiswa';

    protected $fillable = [
        'id_kegiatan',
        'url',
        'jenis_evidence',
        'judul',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(KegiatanMahasiswa::class, 'id_kegiatan');
    }
}
