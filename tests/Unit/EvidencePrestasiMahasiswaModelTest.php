<?php

use App\Enums\JenisEvidencePrestasi;
use App\Models\EvidencePrestasiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\PrestasiMahasiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('jenis evidence prestasi enum returns all defined cases', function () {
    $values = JenisEvidencePrestasi::values();

    expect($values)->toContain(
        'Sertifikat',
        'Piagam',
        'Surat Tugas',
        'Foto Kegiatan',
        'Publikasi',
        'Link Lomba',
        'Lainnya'
    )->toHaveCount(7);
});

test('evidence prestasi mahasiswa model casts jenis_evidence and supports backward-compatible evidence attribute', function () {
    $mahasiswa = Mahasiswa::create([
        'nim' => 33424102,
        'nama_mahasiswa' => 'Budi Santoso',
        'tahun_masuk' => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan' => 48,
    ]);

    $prestasi = PrestasiMahasiswa::create([
        'id_mahasiswa' => $mahasiswa->id,
        'nama_aktivitas' => 'Juara 1 Lomba Web',
        'tahun' => 2024,
    ]);

    // Test creating using 'evidence' attribute alias
    $evidence = EvidencePrestasiMahasiswa::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'evidence' => 'https://storage.polines.ac.id/web.pdf',
        'jenis_evidence' => 'Piagam',
        'judul' => 'Piagam Web',
    ]);

    expect($evidence->url)->toBe('https://storage.polines.ac.id/web.pdf');
    expect($evidence->evidence)->toBe('https://storage.polines.ac.id/web.pdf');
    expect($evidence->jenis_evidence)->toBe(JenisEvidencePrestasi::PIAGAM);

    // Test mutator updates url
    $evidence->evidence = 'https://storage.polines.ac.id/web-rev.pdf';
    expect($evidence->url)->toBe('https://storage.polines.ac.id/web-rev.pdf');

    // Test array representation contains both url and evidence
    $array = $evidence->toArray();
    expect($array)->toHaveKey('url');
    expect($array)->toHaveKey('evidence');
    expect($array['url'])->toBe('https://storage.polines.ac.id/web-rev.pdf');
    expect($array['evidence'])->toBe('https://storage.polines.ac.id/web-rev.pdf');

    // Test relations
    expect($evidence->prestasiMahasiswa->id)->toBe($prestasi->id);
    expect($prestasi->evidence->first()->id)->toBe($evidence->id);
});
