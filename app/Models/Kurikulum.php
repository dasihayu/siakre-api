<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use HasFactory;

    protected $table = 'kurikulum';

    protected $fillable = [
        'id_prodi',
        'nama_kurikulum',
        'berlaku_sampai',
        'sk_kurikulum'
    ];

    // Relasi balik (belongsTo) ke Prodi
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }
}