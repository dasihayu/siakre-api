<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'prodi';

    protected $fillable = [
        'kode_prodi',
        'nama_prodi',
        'akreditasi',
        'jenjang',
        'sk_pendirian',
        'tanggal_sk_pendirian',
        'status_akreditasi',
        'masa_berlaku_akreditasi',
        'id_upps',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_sk_pendirian' => 'date',
            'masa_berlaku_akreditasi' => 'date',
        ];
    }

    /**
     * Relasi ke UPPS (Program Studi berada di bawah naungan UPPS)
     */
    public function upps(): BelongsTo
    {
        return $this->belongsTo(Upps::class, 'id_upps');
    }
}
