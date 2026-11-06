<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cpl extends Model
{
    use HasFactory;

    protected $table = 'cpl';

    protected $fillable = [
        'id_kurikulum',
        'kode_cpl',
        'deskripsi_cpl',
        'kategori',
    ];

    /**
     * Relasi balik (belongsTo) ke Kurikulum
     */
    public function kurikulum(): BelongsTo
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum');
    }

    /** Relasi ke IndikatorLam via pivot cpl_indikator */
    public function indikatorLam(): BelongsToMany
    {
        return $this->belongsToMany(IndikatorLam::class, 'cpl_indikator', 'id_cpl', 'id_indikator_lam');
    }
}
