<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'data_mahasiswa';

    protected $fillable = [
        'nim',
        'id_prodi',
        'nama_mahasiswa',
        'tahun_masuk',
        'jenis_pendaftaran',
        'tanggal_kelulusan',
        'masa_studi_bulan',
        'kewarganegaraan',
        'negara',
        'asal_sekolah_pt',
        'id_status_terkini',
    ];

    protected $appends = [
        'nim_display',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $mahasiswa): void {
            if (! $mahasiswa->tanggal_kelulusan || ! $mahasiswa->tahun_masuk) {
                $mahasiswa->masa_studi_bulan = null;

                return;
            }

            $graduation = Carbon::parse($mahasiswa->tanggal_kelulusan);
            $mahasiswa->masa_studi_bulan = max(
                0,
                (($graduation->year - (int) $mahasiswa->tahun_masuk) * 12) + $graduation->month - 1
            );
        });
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function statusTerkini(): BelongsTo
    {
        return $this->belongsTo(StatusMahasiswa::class, 'id_status_terkini');
    }

    public function getNimDisplayAttribute(): string
    {
        $raw = (string) $this->nim;
        if (preg_match('/^(\d)(\d{2})(\d{2})(\d)(\d{2})$/', $raw, $m)) {
            return "{$m[1]}.{$m[2]}.{$m[3]}.{$m[4]}.{$m[5]}";
        }

        return $raw;
    }

    public function nilaiMahasiswa(): HasMany
    {
        return $this->hasMany(NilaiMahasiswa::class, 'mahasiswa_id');
    }

    public function prestasiMahasiswa(): HasMany
    {
        return $this->hasMany(PrestasiMahasiswa::class, 'id_mahasiswa');
    }
}
