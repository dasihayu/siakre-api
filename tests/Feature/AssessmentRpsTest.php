<?php

use App\Models\Angkatan;
use App\Models\AngkatanKelas;
use App\Models\Cpl;
use App\Models\EnrollmentMataKuliah;
use App\Models\Kelas;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\MataKuliahAngkatanKelas;
use App\Models\NilaiMahasiswa;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can manage RPS through general and mata kuliah endpoints', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);
    $prodi = Prodi::create(['kode_prodi' => 'PR-RPS', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum RPS',
        'berlaku_sampai' => 2030,
    ]);
    $mataKuliah = MataKuliah::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_mata_kuliah' => 'RPS101',
        'nama_mata_kuliah' => 'Perancangan Sistem',
        'sks' => 3,
        'semester' => 4,
        'jenis' => 'Wajib',
        'sks_teori' => 2,
        'sks_praktik' => 1,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson("/api/mata-kuliah/{$mataKuliah->id}/rps", [
            'versi' => '1.0',
            'tahun_berlaku' => 2026,
            'dokumen_url' => 'https://example.test/rps101.pdf',
            'status' => 'Aktif',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.id_mata_kuliah', $mataKuliah->id)
        ->assertJsonPath('data.status', 'Aktif');

    $rpsId = $response->json('data.id');

    $this->postJson("/api/mata-kuliah/{$mataKuliah->id}/rps", [
        'versi' => '3.0',
        'tahun_berlaku' => 2028,
        'status' => 'Invalid',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);

    $this->getJson("/api/mata-kuliah/{$mataKuliah->id}/rps")
        ->assertOk()
        ->assertJsonCount(1, 'data');

    $this->getJson('/api/rps')
        ->assertOk()
        ->assertJsonPath('data.0.versi', '1.0');

    $this->putJson("/api/rps/{$rpsId}", [
        'id_mata_kuliah' => $mataKuliah->id,
        'versi' => '2.0',
        'tahun_berlaku' => 2027,
        'dokumen_url' => 'https://example.test/rps101-v2.pdf',
        'status' => 'Draft',
    ])->assertOk()
        ->assertJsonPath('data.versi', '2.0')
        ->assertJsonPath('data.status', 'Draft');

    $this->deleteJson("/api/rps/{$rpsId}")->assertOk();
    $this->assertDatabaseMissing('rps', ['id' => $rpsId]);

    $this->assertDatabaseHas('mata_kuliah', [
        'id' => $mataKuliah->id,
        'semester' => 4,
        'jenis' => 'Wajib',
        'sks_teori' => 2,
        'sks_praktik' => 1,
    ]);
});

test('CPL assessment and education gap use weighted grades and available cohort data', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);
    $prodi = Prodi::create(['kode_prodi' => 'PR-CPL', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Assessment',
        'berlaku_sampai' => 2030,
    ]);
    $cpl = Cpl::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_cpl' => 'CPL-A1',
        'deskripsi_cpl' => 'Menerapkan konsep komputasi',
    ]);
    $courseTheory = MataKuliah::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_mata_kuliah' => 'AS101',
        'nama_mata_kuliah' => 'Analisis Sistem',
        'sks' => 3,
    ]);
    $coursePractice = MataKuliah::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_mata_kuliah' => 'AS102',
        'nama_mata_kuliah' => 'Praktik Sistem',
        'sks' => 3,
    ]);
    $courseTheory->cpl()->attach($cpl->id, ['bobot' => 25]);
    $coursePractice->cpl()->attach($cpl->id, ['bobot' => 75]);

    $cohort = Angkatan::create(['angkatan' => 2024, 'jumlah_pendaftar' => 2]);
    $student = Mahasiswa::create([
        'nim' => 24000001,
        'nama_mahasiswa' => 'Mahasiswa Uji',
        'tahun_masuk' => 2024,
        'jenis_pendaftaran' => 'SNBT',
        'tanggal_kelulusan' => '2028-07-01',
        'masa_studi_bulan' => 48,
    ]);
    $academicYear = TahunAkademik::create([
        'tahun' => '2024/2025',
        'semester' => 'Ganjil',
        'is_active' => true,
    ]);
    $class = Kelas::create(['kelas' => 'IF-4A']);
    $cohortClass = AngkatanKelas::create([
        'kelas_id' => $class->id,
        'angkatan_id' => $cohort->id,
        'prodi_id' => $prodi->id,
        'tahun_akademik_id' => $academicYear->id,
    ]);

    foreach ([[$courseTheory, 80, 'A', 4], [$coursePractice, 60, 'BC', 2.5]] as [$course, $score, $letter, $weight]) {
        $enrollment = EnrollmentMataKuliah::create([
            'tahun_akademik_id' => $academicYear->id,
            'mata_kuliah_id' => $course->id,
        ]);
        $courseOffering = MataKuliahAngkatanKelas::create([
            'enrollment_mata_kuliah_id' => $enrollment->id,
            'angkatan_kelas_id' => $cohortClass->id,
        ]);
        NilaiMahasiswa::create([
            'mata_kuliah_angkatan_kelas_id' => $courseOffering->id,
            'mahasiswa_id' => $student->id,
            'tahun_akademik_id' => $academicYear->id,
            'nilai_angka' => $score,
            'nilai_huruf' => $letter,
            'bobot' => $weight,
        ]);
    }

    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/assessment/cpl?id_kurikulum={$kurikulum->id}&angkatan=2024")
        ->assertOk()
        ->assertJsonPath('data.cpl.0.skor_aktual', 65)
        ->assertJsonPath('data.cpl.0.status', 'Belum Tercapai')
        ->assertJsonPath('data.cpl.0.jumlah_mahasiswa', 1);

    $gapResponse = $this->getJson("/api/assessment/pendidikan/gap?id_kurikulum={$kurikulum->id}&angkatan=2024");

    $gapResponse->assertOk()
        ->assertJsonPath('data.criteria.0.skor_aktual', 65)
        ->assertJsonPath('data.criteria.0.gap', -5)
        ->assertJsonPath('data.criteria.0.status', 'Belum Tercapai')
        ->assertJsonPath('data.criteria.1.skor_aktual', 100)
        ->assertJsonPath('data.criteria.2.skor_aktual', 3.25)
        ->assertJsonPath('data.criteria.3.skor_aktual', 50);
});

test('assessment endpoints reject invalid filters and unauthenticated access', function () {
    $this->getJson('/api/assessment/cpl')->assertUnauthorized();
    $this->getJson('/api/assessment/pendidikan/gap')->assertUnauthorized();

    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/assessment/cpl?id_kurikulum=99999&angkatan=2024')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['id_kurikulum']);
});
