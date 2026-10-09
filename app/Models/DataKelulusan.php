<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKelulusan extends Model
{
    use HasFactory;

    protected $table = 'data_kelulusan';

    protected $fillable = [
        'id_mahasiswa',
        'tanggal_kelulusan',
        'ipk',
        'masa_studi_bulan',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }
}