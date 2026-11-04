<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CplIndikator extends Model
{
    use HasFactory;

    protected $table = 'cpl_indikator';

    protected $fillable = [
        'id_cpl',
        'id_indikator_lam',
    ];

    public function cpl(): BelongsTo
    {
        return $this->belongsTo(Cpl::class, 'id_cpl');
    }

    public function indikatorLam(): BelongsTo
    {
        return $this->belongsTo(IndikatorLam::class, 'id_indikator_lam');
    }
}
