<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaUpps extends Model
{
    use HasFactory;

    protected $table = 'anggota_upps';

    protected $fillable = [
        'id_upps',
        'nama',
        'jabatan'
    ];

    // Relasi balik ke UPPS
    public function upps()
    {
        return $this->belongsTo(Upps::class, 'id_upps');
    }
}