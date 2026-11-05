<?php

namespace App\Enums;

enum JenisDokumenBuktiPembelajaran: string
{
    case RPS = 'RPS';
    case KONTRAK_KULIAH = 'Kontrak Kuliah';
    case PRESENSI = 'Presensi';
    case SOAL_UJIAN = 'Soal Ujian';
}

