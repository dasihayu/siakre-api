<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah';

    protected $fillable = [
        'id_kurikulum',
        'kode_mata_kuliah',
        'nama_mata_kuliah',
        'sks',
    ];

    // Relasi balik ke Kurikulum
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum');
    }

    // Relasi Many-to-Many ke CPL (Disiapkan untuk fitur Mapping CPL nanti)
    public function cpl()
    {
        return $this->belongsToMany(Cpl::class, 'mata_kuliah_cpl', 'id_mata_kuliah', 'id_cpl');
    }
}