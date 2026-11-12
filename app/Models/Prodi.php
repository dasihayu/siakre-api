<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'prodi';

    protected $fillable = [
        'kode_prodi',
        'nama_prodi',
        'akreditasi',
        'id_upps',
    ];

    public function upps()
    {
        return $this->belongsTo(Upps::class, 'id_upps');
    }
}
