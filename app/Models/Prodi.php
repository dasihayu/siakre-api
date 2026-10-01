<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    // Beritahu Laravel kalau nama tabelnya 'prodi', bukan 'prodis'
    protected $table = 'prodi';

    protected $fillable = [
        'kode_prodi',
        'nama_prodi',
        'akreditasi'
    ];
}
