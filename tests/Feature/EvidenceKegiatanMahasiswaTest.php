<?php

use App\Models\EvidenceKegiatanMahasiswa;
use App\Models\KegiatanMahasiswa;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('kegiatan mahasiswa response includes linked evidence', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mahasiswa = Mahasiswa::create([
        'nim' => 33424701,
        'nama_mahasiswa' => 'Mahasiswa Evidence',
        'tahun_masuk' => 2024,
        'jenis_pendaftaran' => 'Reguler',
    ]);
    $tahunAkademik = TahunAkademik::create(['tahun' => '2025']);
    $kegiatan = KegiatanMahasiswa::create([
        'mahasiswa_id' => $mahasiswa->id,
        'tahun_akademik_id' => $tahunAkademik->id,
        'nama_kegiatan' => 'Lomba',
        'jenis_kegiatan' => 'Kompetisi',
        'peran' => 'Peserta',
        'tingkat' => 'Nasional',
        'tanggal_mulai' => '2025-01-01',
        'penyelenggara' => 'Kampus',
        'status_validasi' => 'Pending',
    ]);
    EvidenceKegiatanMahasiswa::create([
        'id_kegiatan' => $kegiatan->id,
        'url' => 'https://example.test/sertifikat.pdf',
        'jenis_evidence' => 'Sertifikat',
        'judul' => 'Sertifikat Peserta',
    ]);

    $this->withHeader('Authorization', '******')
        ->getJson("/api/kegiatan-mahasiswa/{$kegiatan->id}")
        ->assertOk()
        ->assertJsonPath('data.evidences.0.judul', 'Sertifikat Peserta')
        ->assertJsonPath('data.evidences.0.url', 'https://example.test/sertifikat.pdf');
});
