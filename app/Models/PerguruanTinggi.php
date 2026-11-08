<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerguruanTinggi extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'kode_pt',
        'nama_pt',
        'alamat',
        'pimpinan',
        'visi',
        'misi',
        'sk_pendirian',
        'tanggal_sk_pendirian',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'tanggal_sk_pendirian' => 'date:Y-m-d',
    ];
}
