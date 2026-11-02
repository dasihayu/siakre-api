<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Upps extends Model
{
    use HasFactory;

    protected $table = 'upps';

    protected $fillable = [
        'id_pt',
        'kode_upps',
        'nama_upps',
        'pimpinan_upps',
        'prodi_id',
        'jenis',
    ];

    // Relasi ke Perguruan Tinggi
    public function perguruanTinggi(): BelongsTo
    {
        return $this->belongsTo(PerguruanTinggi::class, 'id_pt');
    }

    // Relasi ke Prodi lama (1 UPPS berelasi ke prodi_id)
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    // Relasi 1 UPPS menaungi banyak Prodi baru via id_upps
    public function prodis(): HasMany
    {
        return $this->hasMany(Prodi::class, 'id_upps');
    }
}
