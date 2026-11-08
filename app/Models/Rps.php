<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rps extends Model
{
    use HasFactory;

    protected $table = 'rps';

    protected $fillable = [
        'id_mata_kuliah',
        'versi',
        'tahun_berlaku',
        'dokumen_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tahun_berlaku' => 'integer',
        ];
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'id_mata_kuliah');
    }
}
