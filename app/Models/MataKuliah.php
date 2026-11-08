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
        'semester',
        'jenis',
        'sks_teori',
        'sks_praktik',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum');
    }

    public function cpl()
    {
        return $this->belongsToMany(Cpl::class, 'mata_kuliah_cpl', 'id_mata_kuliah', 'id_cpl')
            ->withPivot('bobot')
            ->withTimestamps();
    }

    public function rps()
    {
        return $this->hasMany(Rps::class, 'id_mata_kuliah');
    }
}
