<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'Prodi';

    protected $fillable = [
        'kode_prodi',
        'nama_prodi',
        'akreditasi'
    ];

    public function upps()
    {
        return $this->hasMany(Upps::class, 'prodi_id');
    }
}
