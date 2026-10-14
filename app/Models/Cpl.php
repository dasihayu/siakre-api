<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpl extends Model
{
    use HasFactory;

    protected $table = 'cpl';

    protected $fillable = [
        'id_kurikulum',
        'kode_cpl',
        'deskripsi_cpl',
        'kategori'
    ];

    // Relasi balik (belongsTo) ke Kurikulum
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum');
    }
}