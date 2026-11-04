<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndikatorLam extends Model
{
    use HasFactory;

    protected $table = 'indikator_lam';

    protected $fillable = [
        'kode_indikator',
        'nama_indikator',
        'deskripsi',
        'standar',
    ];

    /** Relasi ke CPL via pivot cpl_indikator */
    public function cpl(): BelongsToMany
    {
        return $this->belongsToMany(Cpl::class, 'cpl_indikator', 'id_indikator_lam', 'id_cpl');
    }

    /** Semua pemetaan evidence ke indikator ini */
    public function evidenceIndikator(): HasMany
    {
        return $this->hasMany(EvidenceIndikator::class, 'id_indikator_lam');
    }
}
