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
        'jabatan',
        'parent_id',
        'urutan_tampil',
        'periode_mulai',
        'periode_selesai',
    ];

    // Relasi balik ke UPPS
    public function upps()
    {
        return $this->belongsTo(Upps::class, 'id_upps');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('urutan_tampil');
    }
}
