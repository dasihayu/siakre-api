<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['kelas'];

    protected $appends = ['nama', 'nama_kelas'];

    public function getNamaAttribute(): string
    {
        return (string) $this->kelas;
    }

    public function getNamaKelasAttribute(): string
    {
        return (string) $this->kelas;
    }

    public function angkatanKelas()
    {
        return $this->hasMany(AngkatanKelas::class, 'kelas_id');
    }
}
