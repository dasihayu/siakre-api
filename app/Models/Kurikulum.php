<?php

namespace App\Models;

use App\Enums\StatusKurikulum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use HasFactory;

    protected $table = 'kurikulum';

    protected $fillable = [
        'id_prodi',
        'nama_kurikulum',
        'tahun_mulai',
        'berlaku_sampai',
        'status',
        'dokumen_url',
        'profil_lulusan',
        'sk_kurikulum',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusKurikulum::class,
            'tahun_mulai' => 'integer',
            'berlaku_sampai' => 'integer',
        ];
    }

    // Relasi balik (belongsTo) ke Prodi
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function mataKuliah()
    {
        return $this->hasMany(MataKuliah::class, 'id_kurikulum');
    }
}
