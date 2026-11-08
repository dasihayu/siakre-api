<?php

namespace App\Enums;

enum JenisEvidencePrestasi: string
{
    case SERTIFIKAT = 'Sertifikat';
    case PIAGAM = 'Piagam';
    case SURAT_TUGAS = 'Surat Tugas';
    case FOTO_KEGIATAN = 'Foto Kegiatan';
    case PUBLIKASI = 'Publikasi';
    case LINK_LOMBA = 'Link Lomba';
    case LAINNYA = 'Lainnya';

    /**
     * Mengambil seluruh nilai enum jenis evidence prestasi.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
