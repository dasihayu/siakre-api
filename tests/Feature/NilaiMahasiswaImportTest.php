<?php

use App\Models\Angkatan;
use App\Models\AngkatanKelas;
use App\Models\EnrollmentKelas;
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
use App\Services\SpreadsheetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

function getImportAuthToken(): string
{
    $user = User::factory()->create();

    return auth('api')->login($user);
}

function setupCourseAndClass(string $kodeMk = 'MK-IMP-01', string $namaKelas = 'TRK-1A'): array
{
    $prodi = Prodi::firstOrCreate(
        ['kode_prodi' => 'PR-IMP'],
        [
            'nama_prodi' => 'Teknologi Rekayasa Komputer',
            'akreditasi' => 'Unggul',
        ]
    );

    $kurikulum = Kurikulum::firstOrCreate(
        ['sk_kurikulum' => 'SK-KM-2024'],
        [
            'id_prodi' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum Merdeka 2024',
            'berlaku_sampai' => 2028,
        ]
    );

    $mk = MataKuliah::firstOrCreate(
        ['kode_mata_kuliah' => $kodeMk],
        [
            'id_kurikulum' => $kurikulum->id,
            'nama_mata_kuliah' => 'Mata Kuliah Import Test ' . $kodeMk,
            'sks' => 3,
        ]
    );

    $ta = TahunAkademik::firstOrCreate(
        ['tahun' => '2025/2026', 'semester' => 'Ganjil'],
        ['is_active' => true]
    );

    $kelas = Kelas::firstOrCreate(['kelas' => $namaKelas]);
    $angkatan = Angkatan::firstOrCreate(['angkatan' => 2025]);

    $emk = EnrollmentMataKuliah::firstOrCreate([
        'tahun_akademik_id' => $ta->id,
        'mata_kuliah_id' => $mk->id,
    ]);

    $ak = AngkatanKelas::firstOrCreate([
        'kelas_id' => $kelas->id,
        'angkatan_id' => $angkatan->id,
        'prodi_id' => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ]);

    $mkAk = MataKuliahAngkatanKelas::firstOrCreate([
        'enrollment_mata_kuliah_id' => $emk->id,
        'angkatan_kelas_id' => $ak->id,
    ]);

    return compact('prodi', 'kurikulum', 'mk', 'ta', 'kelas', 'angkatan', 'emk', 'ak', 'mkAk');
}

test('can download single-class import template in csv format', function () {
    $token = getImportAuthToken();
    $setup = setupCourseAndClass();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->get('/api/nilai-mahasiswa/template?format=csv&mode=single');

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('text/csv');
    expect($response->headers->get('content-disposition'))->toContain('template_nilai_single.csv');
    expect($response->getContent())->toContain('NIM');
    expect($response->getContent())->toContain('Nilai Angka');
});

test('can download multi-class import template in xlsx format', function () {
    $token = getImportAuthToken();
    $setup = setupCourseAndClass();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->get('/api/nilai-mahasiswa/template?format=xlsx&mode=multi');

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('spreadsheetml.sheet');
    expect($response->headers->get('content-disposition'))->toContain('template_nilai_multi.xlsx');
});

test('can import nilai mahasiswa from csv in single class mode', function () {
    $token = getImportAuthToken();
    $setup = setupCourseAndClass();
    $mkAk = $setup['mkAk'];

    $mhs1 = Mahasiswa::create(['nim' => 43325101, 'nama_mahasiswa' => 'Mahasiswa Satu', 'tahun_masuk' => 2025, 'jenis_pendaftaran' => 'Reguler']);
    $mhs2 = Mahasiswa::create(['nim' => 43325102, 'nama_mahasiswa' => 'Mahasiswa Dua', 'tahun_masuk' => 2025, 'jenis_pendaftaran' => 'Reguler']);

    $csvData = "NIM,Nama Mahasiswa,Nilai Angka,Link Nilai,Link Evidence\n" .
        "43325101,Mahasiswa Satu,85.50,https://docs.google.com/sheet1,https://drive.google.com/ev1\n" .
        "43325102,Mahasiswa Dua,74.00,https://docs.google.com/sheet2,\n";

    $file = UploadedFile::fake()->createWithContent('nilai.csv', $csvData);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/nilai-mahasiswa/import', [
            'file' => $file,
            'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'total_rows' => 2,
                'imported_count' => 2,
                'updated_count' => 0,
                'skipped_count' => 0,
                'failed_count' => 0,
            ],
        ]);

    $this->assertDatabaseHas('nilai_mahasiswa', [
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'mahasiswa_id' => $mhs1->id,
        'nilai_angka' => 85.50,
        'nilai_huruf' => 'A',
        'bobot' => 4.00,
    ]);

    $this->assertDatabaseHas('nilai_mahasiswa', [
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'mahasiswa_id' => $mhs2->id,
        'nilai_angka' => 74.00,
        'nilai_huruf' => 'B',
        'bobot' => 3.00,
    ]);
});

test('can import nilai mahasiswa from xlsx in multi class mode', function () {
    $token = getImportAuthToken();
    $setupA = setupCourseAndClass('MK-MULTI-01', 'TRK-3A');
    $setupB = setupCourseAndClass('MK-MULTI-01', 'TRK-3B');

    $mhsA = Mahasiswa::create(['nim' => 43325201, 'nama_mahasiswa' => 'Mahasiswa Kelas A', 'tahun_masuk' => 2025, 'jenis_pendaftaran' => 'Reguler']);
    $mhsB = Mahasiswa::create(['nim' => 43325202, 'nama_mahasiswa' => 'Mahasiswa Kelas B', 'tahun_masuk' => 2025, 'jenis_pendaftaran' => 'Reguler']);

    $service = new SpreadsheetService();
    $headers = ['Kode Mata Kuliah', 'Kelas', 'NIM', 'Nilai Angka'];
    $rows = [
        ['MK-MULTI-01', 'TRK-3A', '43325201', 90.00],
        ['MK-MULTI-01', 'TRK-3B', '43325202', 65.00],
    ];
    $xlsxContent = $service->createXlsx($headers, $rows);

    $file = UploadedFile::fake()->createWithContent('nilai_multi.xlsx', $xlsxContent);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/nilai-mahasiswa/import', [
            'file' => $file,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'total_rows' => 2,
                'imported_count' => 2,
                'failed_count' => 0,
            ],
        ]);

    $this->assertDatabaseHas('nilai_mahasiswa', [
        'mata_kuliah_angkatan_kelas_id' => $setupA['mkAk']->id,
        'mahasiswa_id' => $mhsA->id,
        'nilai_angka' => 90.00,
        'nilai_huruf' => 'A',
    ]);

    $this->assertDatabaseHas('nilai_mahasiswa', [
        'mata_kuliah_angkatan_kelas_id' => $setupB['mkAk']->id,
        'mahasiswa_id' => $mhsB->id,
        'nilai_angka' => 65.00,
        'nilai_huruf' => 'B',
    ]);
});

test('handles data duplication safely: updates existing record when duplicate_action is update', function () {
    $token = getImportAuthToken();
    $setup = setupCourseAndClass();
    $mkAk = $setup['mkAk'];
    $ta = $setup['ta'];

    $mhs = Mahasiswa::create(['nim' => 43325301, 'nama_mahasiswa' => 'Mahasiswa Duplicate Test', 'tahun_masuk' => 2025, 'jenis_pendaftaran' => 'Reguler']);

    // Nilai awal di database
    NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'mahasiswa_id' => $mhs->id,
        'tahun_akademik_id' => $ta->id,
        'nilai_angka' => 60.00,
        'nilai_huruf' => 'BC',
        'bobot' => 2.50,
    ]);

    $csvData = "NIM,Nilai Angka\n" .
        "43325301,88.00\n";

    $file = UploadedFile::fake()->createWithContent('update_nilai.csv', $csvData);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/nilai-mahasiswa/import', [
            'file' => $file,
            'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
            'duplicate_action' => 'update',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'total_rows' => 1,
                'imported_count' => 0,
                'updated_count' => 1,
                'skipped_count' => 0,
            ],
        ]);

    $this->assertDatabaseHas('nilai_mahasiswa', [
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'mahasiswa_id' => $mhs->id,
        'nilai_angka' => 88.00,
        'nilai_huruf' => 'A',
    ]);
});

test('handles data duplication safely: skips existing record when duplicate_action is skip', function () {
    $token = getImportAuthToken();
    $setup = setupCourseAndClass();
    $mkAk = $setup['mkAk'];
    $ta = $setup['ta'];

    $mhs = Mahasiswa::create(['nim' => 43325302, 'nama_mahasiswa' => 'Mahasiswa Skip Test', 'tahun_masuk' => 2025, 'jenis_pendaftaran' => 'Reguler']);

    NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'mahasiswa_id' => $mhs->id,
        'tahun_akademik_id' => $ta->id,
        'nilai_angka' => 70.00,
        'nilai_huruf' => 'B',
        'bobot' => 3.00,
    ]);

    $csvData = "NIM,Nilai Angka\n" .
        "43325302,95.00\n";

    $file = UploadedFile::fake()->createWithContent('skip_nilai.csv', $csvData);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/nilai-mahasiswa/import', [
            'file' => $file,
            'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
            'duplicate_action' => 'skip',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'total_rows' => 1,
                'imported_count' => 0,
                'updated_count' => 0,
                'skipped_count' => 1,
            ],
        ]);

    // Nilai tetap 70 (tidak berubah)
    $this->assertDatabaseHas('nilai_mahasiswa', [
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'mahasiswa_id' => $mhs->id,
        'nilai_angka' => 70.00,
    ]);
});

test('reports error and aborts when duplicate_action is fail and duplicate exists', function () {
    $token = getImportAuthToken();
    $setup = setupCourseAndClass();
    $mkAk = $setup['mkAk'];
    $ta = $setup['ta'];

    $mhs = Mahasiswa::create(['nim' => 43325303, 'nama_mahasiswa' => 'Mahasiswa Fail Test', 'tahun_masuk' => 2025, 'jenis_pendaftaran' => 'Reguler']);

    NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'mahasiswa_id' => $mhs->id,
        'tahun_akademik_id' => $ta->id,
        'nilai_angka' => 70.00,
    ]);

    $csvData = "NIM,Nilai Angka\n" .
        "43325303,95.00\n";

    $file = UploadedFile::fake()->createWithContent('fail_nilai.csv', $csvData);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/nilai-mahasiswa/import', [
            'file' => $file,
            'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
            'duplicate_action' => 'fail',
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'code' => 422,
        ]);
});

test('handles non-existent NIM gracefully in errors list', function () {
    $token = getImportAuthToken();
    $setup = setupCourseAndClass();
    $mkAk = $setup['mkAk'];

    $csvData = "NIM,Nilai Angka\n" .
        "99999999,85.00\n";

    $file = UploadedFile::fake()->createWithContent('not_found.csv', $csvData);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/nilai-mahasiswa/import', [
            'file' => $file,
            'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'total_rows' => 1,
                'imported_count' => 0,
                'failed_count' => 1,
            ],
        ]);
});
