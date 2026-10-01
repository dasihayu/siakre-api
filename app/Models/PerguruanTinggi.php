<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerguruanTinggi extends Model
{
    // Daftarkan kolom yang boleh diisi data
    protected $fillable = [
        'kode_pt',
        'nama_pt',
        'alamat',
        'pimpinan',
        'visi',
        'misi'
    ];
}
