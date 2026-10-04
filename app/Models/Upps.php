<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upps extends Model
{
    use HasFactory;

    protected $table = 'Upps';
    
    protected $fillable = [
        'id_pt', 
        'kode_upps', 
        'nama_upps', 
        'pimpinan_upps', 
        'prodi_id', 
        'jenis'
    ];

    // Relasi ke Perguruan Tinggi
    public function perguruanTinggi()
    {
        return $this->belongsTo(PerguruanTinggi::class, 'id_pt');
    }

    // Relasi ke Prodi
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}
