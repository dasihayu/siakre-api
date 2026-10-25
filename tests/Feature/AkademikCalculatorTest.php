<?php

use App\Models\Angkatan;
use App\Models\AngkatanKelas;
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
use App\Services\AkademikCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('konversi bobot nilai sesuai standar akademik', function () {
    expect(AkademikCalculatorService::bobotNilai('A'))->toBe(4.0);
    expect(AkademikCalculatorService::bobotNilai('AB'))->toBe(3.5);
    expect(AkademikCalculatorService::bobotNilai('B'))->toBe(3.0);
    expect(AkademikCalculatorService::bobotNilai('BC'))->toBe(2.5);
    expect(AkademikCalculatorService::bobotNilai('C'))->toBe(2.0);
    expect(AkademikCalculatorService::bobotNilai('D'))->toBe(1.0);
    expect(AkademikCalculatorService::bobotNilai('E'))->toBe(0.0);
});

test('perhitungan IP semester menghasilkan 3.80 sesuai contoh tabel', function () {
    // Data mata kuliah dari contoh prompt user: total SKS = 20, total kredit = 76, IP = 3.80
    $semester1 = [
        ['nama' => 'Kewarganegaraan', 'sks' => 2, 'nilai' => 'AB'],
        ['nama' => 'Bahasa Inggris I', 'sks' => 1, 'nilai' => 'A'],
        ['nama' => 'Statistika', 'sks' => 2, 'nilai' => 'A'],
        ['nama' => 'Jaringan Komputer I', 'sks' => 3, 'nilai' => 'AB'],
        ['nama' => 'Sistem Basis Data II', 'sks' => 3, 'nilai' => 'A'],
        ['nama' => 'Pemrograman Berorientasi Objek', 'sks' => 3, 'nilai' => 'A'],
        ['nama' => 'Kecerdasan Buatan', 'sks' => 3, 'nilai' => 'A'],
        ['nama' => 'Teknik Digital dan Sistem Tertanam', 'sks' => 3, 'nilai' => 'AB'],
    ];

    $res = AkademikCalculatorService::hitungIP($semester1);

    expect($res['total_sks'])->toBe(20);
    expect($res['total_kredit'])->toBe(76.0);
    expect($res['ip'])->toBe(3.80);
});

test('perhitungan IPK kumulatif dari beberapa semester menghasilkan 3.75 sesuai contoh rumus', function () {
    // Sesuai contoh:
    // Semester 1: total SKS = 20, total kredit = 74, IP = 3.70
    // Semester 2: total SKS = 22, total kredit = 83.6, IP = 3.80
    // Kumulatif: total SKS = 42, total kredit = 157.6, IPK = 3.75
    $mkSemester1 = [
        ['kode' => 'MK-101', 'sks' => 20, 'bobot' => 3.70],
    ];
    $mkSemester2 = [
        ['kode' => 'MK-201', 'sks' => 22, 'bobot' => 3.80],
    ];

    $res = AkademikCalculatorService::hitungIPK(array_merge($mkSemester1, $mkSemester2));

    expect($res['total_sks'])->toBe(42);
    expect($res['total_kredit'])->toBe(157.6);
    expect($res['ipk'])->toBe(3.75);
});

test('evaluasi mata kuliah mengulang memilih nilai terbaik dan tidak menduplikasi SKS pada IPK', function () {
    // Mahasiswa mengambil Basis Data (3 SKS) di Semester 1 dapat C (2.0)
    // Kemudian mengulang di Semester 3 dapat A (4.0)
    // Serta mengambil Algoritma (3 SKS) dapat B (3.0)
    $courses = [
        ['kode_mata_kuliah' => 'MK-DB', 'nama_mata_kuliah' => 'Basis Data', 'sks' => 3, 'bobot' => 2.0, 'nilai_huruf' => 'C'],
        ['kode_mata_kuliah' => 'MK-ALGO', 'nama_mata_kuliah' => 'Algoritma', 'sks' => 3, 'bobot' => 3.0, 'nilai_huruf' => 'B'],
        ['kode_mata_kuliah' => 'MK-DB', 'nama_mata_kuliah' => 'Basis Data', 'sks' => 3, 'bobot' => 4.0, 'nilai_huruf' => 'A'],
    ];

    $res = AkademikCalculatorService::hitungIPK($courses, 'terbaik');

    // Total SKS harus 6 (bukan 9)
    expect($res['total_sks'])->toBe(6);
    // Total kredit = (3 * 4.0) + (3 * 3.0) = 12 + 9 = 21.0
    expect($res['total_kredit'])->toBe(21.0);
    // IPK = 21 / 6 = 3.50
    expect($res['ipk'])->toBe(3.50);

    // Verifikasi flag mata kuliah yang tidak diperhitungkan
    $items = $res['items'];
    expect($items[0]['is_diperhitungkan_ipk'])->toBeFalse();
    expect($items[0]['keterangan'])->toContain('Mata kuliah diulang');
    expect($items[1]['is_diperhitungkan_ipk'])->toBeTrue();
    expect($items[2]['is_diperhitungkan_ipk'])->toBeTrue();
});

test('endpoint ringkasan akademik mengembalikan perkembangan per semester dan IPK kumulatif', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR-AKAD', 'nama_prodi' => 'TRK', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'KM-2024', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK-01']);
    $kelas = Kelas::create(['kelas' => 'TRK-1A']);
    $angkatan = Angkatan::create(['angkatan' => 2024]);
    $mhs = Mahasiswa::create(['nim' => 43325999, 'nama_mahasiswa' => 'Budi Santoso', 'tahun_masuk' => 2024, 'jenis_pendaftaran' => 'Reguler']);

    $ta1 = TahunAkademik::create(['tahun' => '2024/2025', 'semester' => 'Ganjil', 'is_active' => false]);
    $ta2 = TahunAkademik::create(['tahun' => '2024/2025', 'semester' => 'Genap', 'is_active' => true]);

    $mk1 = MataKuliah::create(['id_kurikulum' => $kurikulum->id, 'kode_mata_kuliah' => 'MK-1', 'nama_mata_kuliah' => 'Matkul 1', 'sks' => 3]);
    $mk2 = MataKuliah::create(['id_kurikulum' => $kurikulum->id, 'kode_mata_kuliah' => 'MK-2', 'nama_mata_kuliah' => 'Matkul 2', 'sks' => 3]);

    $emk1 = EnrollmentMataKuliah::create(['tahun_akademik_id' => $ta1->id, 'mata_kuliah_id' => $mk1->id]);
    $emk2 = EnrollmentMataKuliah::create(['tahun_akademik_id' => $ta2->id, 'mata_kuliah_id' => $mk2->id]);

    $ak1 = AngkatanKelas::create(['kelas_id' => $kelas->id, 'angkatan_id' => $angkatan->id, 'prodi_id' => $prodi->id, 'tahun_akademik_id' => $ta1->id]);
    $ak2 = AngkatanKelas::create(['kelas_id' => $kelas->id, 'angkatan_id' => $angkatan->id, 'prodi_id' => $prodi->id, 'tahun_akademik_id' => $ta2->id]);

    $mkAk1 = MataKuliahAngkatanKelas::create(['enrollment_mata_kuliah_id' => $emk1->id, 'angkatan_kelas_id' => $ak1->id]);
    $mkAk2 = MataKuliahAngkatanKelas::create(['enrollment_mata_kuliah_id' => $emk2->id, 'angkatan_kelas_id' => $ak2->id]);

    // Semester 1: SKS 3, Bobot 4.0 (A) -> IP 4.0
    NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk1->id,
        'mahasiswa_id' => $mhs->id,
        'tahun_akademik_id' => $ta1->id,
        'nilai_angka' => 85.00,
        'nilai_huruf' => 'A',
        'bobot' => 4.00,
    ]);

    // Semester 2: SKS 3, Bobot 3.0 (B) -> IP 3.0
    NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk2->id,
        'mahasiswa_id' => $mhs->id,
        'tahun_akademik_id' => $ta2->id,
        'nilai_angka' => 70.00,
        'nilai_huruf' => 'B',
        'bobot' => 3.00,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/mahasiswa/{$mhs->id}/ringkasan-akademik");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Ringkasan akademik mahasiswa berhasil diambil',
            'data' => [
                'total_sks' => 6,
                'total_kredit' => 21.0,
                'ipk' => 3.5,
                'predikat' => 'Sangat Memuaskan',
            ],
        ]);

    $perkembangan = $response->json('data.perkembangan_semester');
    expect(count($perkembangan))->toBe(2);
    expect($perkembangan[0]['ip_semester'])->toEqual(4.0);
    expect($perkembangan[1]['ip_semester'])->toEqual(3.0);
    expect($perkembangan[1]['ipk_kumulatif'])->toEqual(3.5);
});
