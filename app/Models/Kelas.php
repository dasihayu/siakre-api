<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['kelas'];

    public function angkatanKelas()
    {
        return $this->hasMany(AngkatanKelas::class, 'kelas_id');
    }
}
