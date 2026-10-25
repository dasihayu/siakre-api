<?php

use App\Http\Controllers\AnggotaUppsController;
use App\Http\Controllers\AngkatanController;
use App\Http\Controllers\AngkatanKelasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CplController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\EnrollmentKelasController;
use App\Http\Controllers\EnrollmentMataKuliahController;
use App\Http\Controllers\EvidencePrestasiMahasiswaController;
use App\Http\Controllers\JenisPrestasiController;
use App\Http\Controllers\KegiatanMahasiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahAngkatanKelasController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\MataKuliahCplController;
use App\Http\Controllers\NilaiMahasiswaController;
use App\Http\Controllers\PerguruanTinggiController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PrestasiMahasiswaController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatusMahasiswaController;
use App\Http\Controllers\TahunAkademikController;
use App\Http\Controllers\UppsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/google', [AuthController::class, 'googleLogin'])->name('google.login');

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

Route::middleware(['auth:api', 'role:ADMINISTRATOR'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:user.read');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:user.create');
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('permission:user.read');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:user.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:user.delete');

    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:role.read');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:role.create');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('permission:role.read');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:role.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:role.delete');

    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:permission.read');
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:permission.create');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->middleware('permission:permission.read');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->middleware('permission:permission.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:permission.delete');
});

Route::middleware(['auth:api'])->group(function () {
    // --- CRUD Master Perguruan Tinggi ---
    Route::get('/perguruan-tinggi', [PerguruanTinggiController::class, 'index']);
    Route::post('/perguruan-tinggi', [PerguruanTinggiController::class, 'store']);
    Route::get('/perguruan-tinggi/{id}', [PerguruanTinggiController::class, 'show']);
    Route::put('/perguruan-tinggi/{id}', [PerguruanTinggiController::class, 'update']);
    Route::delete('/perguruan-tinggi/{id}', [PerguruanTinggiController::class, 'destroy']);

    // --- CRUD Master Program Studi (Prodi) ---
    Route::get('/prodi', [ProdiController::class, 'index']);
    Route::post('/prodi', [ProdiController::class, 'store']);
    Route::get('/prodi/{id}', [ProdiController::class, 'show']);
    Route::put('/prodi/{id}', [ProdiController::class, 'update']);
    Route::delete('/prodi/{id}', [ProdiController::class, 'destroy']);

    // --- CRUD Master UPPS ---
    Route::get('/upps', [UppsController::class, 'index']);
    Route::post('/upps', [UppsController::class, 'store']);
    Route::get('/upps/{id}', [UppsController::class, 'show']);
    Route::put('/upps/{id}', [UppsController::class, 'update']);
    Route::delete('/upps/{id}', [UppsController::class, 'destroy']);

    // --- CRUD Master Kurikulum ---
    Route::get('/kurikulum', [KurikulumController::class, 'index']);
    Route::post('/kurikulum', [KurikulumController::class, 'store']);
    Route::get('/kurikulum/{id}', [KurikulumController::class, 'show']);
    Route::put('/kurikulum/{id}', [KurikulumController::class, 'update']);
    Route::delete('/kurikulum/{id}', [KurikulumController::class, 'destroy']);
    Route::get('/kurikulum/{id}/mata-kuliah', [KurikulumController::class, 'mataKuliah']);

    // --- CRUD Master CPL ---
    Route::get('/cpl', [CplController::class, 'index']);
    Route::post('/cpl', [CplController::class, 'store']);
    Route::get('/cpl/{id}', [CplController::class, 'show']);
    Route::put('/cpl/{id}', [CplController::class, 'update']);
    Route::delete('/cpl/{id}', [CplController::class, 'destroy']);

    // --- CRUD Master Mata Kuliah ---
    Route::get('/mata-kuliah', [MataKuliahController::class, 'index']);
    Route::post('/mata-kuliah', [MataKuliahController::class, 'store']);
    Route::get('/mata-kuliah/{id}', [MataKuliahController::class, 'show']);
    Route::put('/mata-kuliah/{id}', [MataKuliahController::class, 'update']);
    Route::delete('/mata-kuliah/{id}', [MataKuliahController::class, 'destroy']);

    // --- Mapping Mata Kuliah ke CPL ---
    Route::get('/mata-kuliah/{mataKuliahId}/cpl', [MataKuliahCplController::class, 'index']);
    Route::post('/mata-kuliah/{mataKuliahId}/cpl/sync', [MataKuliahCplController::class, 'sync']);

    // --- CRUD Data Mahasiswa ---
    Route::get('/mahasiswa', [MahasiswaController::class, 'index']);
    Route::post('/mahasiswa', [MahasiswaController::class, 'store']);
    Route::get('/mahasiswa/{id}', [MahasiswaController::class, 'show']);
    Route::put('/mahasiswa/{id}', [MahasiswaController::class, 'update']);
    Route::delete('/mahasiswa/{id}', [MahasiswaController::class, 'destroy']);

    // --- CRUD Anggota UPPS ---
    Route::get('/anggota-upps', [AnggotaUppsController::class, 'index']);
    Route::post('/anggota-upps', [AnggotaUppsController::class, 'store']);
    Route::get('/anggota-upps/{id}', [AnggotaUppsController::class, 'show']);
    Route::put('/anggota-upps/{id}', [AnggotaUppsController::class, 'update']);
    Route::delete('/anggota-upps/{id}', [AnggotaUppsController::class, 'destroy']);

    // --- CRUD Master Angkatan ---
    Route::get('/angkatan', [AngkatanController::class, 'index']);
    Route::post('/angkatan', [AngkatanController::class, 'store']);
    Route::get('/angkatan/{id}', [AngkatanController::class, 'show']);
    Route::put('/angkatan/{id}', [AngkatanController::class, 'update']);
    Route::delete('/angkatan/{id}', [AngkatanController::class, 'destroy']);

    // --- CRUD Master Kelas ---
    Route::get('/kelas', [KelasController::class, 'index']);
    Route::post('/kelas', [KelasController::class, 'store']);
    Route::get('/kelas/{id}', [KelasController::class, 'show']);
    Route::put('/kelas/{id}', [KelasController::class, 'update']);
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy']);
    Route::get('/kelas/{id}/mahasiswa', [KelasController::class, 'mahasiswa']);
    Route::get('/kelas/{id}/mata-kuliah', [KelasController::class, 'mataKuliah']);

    // --- CRUD Master Tahun Akademik ---
    Route::get('/tahun-akademik', [TahunAkademikController::class, 'index']);
    Route::post('/tahun-akademik', [TahunAkademikController::class, 'store']);
    Route::get('/tahun-akademik/{id}', [TahunAkademikController::class, 'show']);
    Route::put('/tahun-akademik/{id}', [TahunAkademikController::class, 'update']);
    Route::delete('/tahun-akademik/{id}', [TahunAkademikController::class, 'destroy']);

    // --- CRUD Kegiatan Mahasiswa ---
    Route::get('/kegiatan-mahasiswa', [KegiatanMahasiswaController::class, 'index']);
    Route::post('/kegiatan-mahasiswa', [KegiatanMahasiswaController::class, 'store']);
    Route::get('/kegiatan-mahasiswa/{id}', [KegiatanMahasiswaController::class, 'show']);
    Route::put('/kegiatan-mahasiswa/{id}', [KegiatanMahasiswaController::class, 'update']);
    Route::delete('/kegiatan-mahasiswa/{id}', [KegiatanMahasiswaController::class, 'destroy']);

    // --- CRUD Angkatan Kelas ---
    Route::get('/angkatan-kelas', [AngkatanKelasController::class, 'index']);
    Route::post('/angkatan-kelas', [AngkatanKelasController::class, 'store']);
    Route::get('/angkatan-kelas/{id}', [AngkatanKelasController::class, 'show']);
    Route::put('/angkatan-kelas/{id}', [AngkatanKelasController::class, 'update']);
    Route::delete('/angkatan-kelas/{id}', [AngkatanKelasController::class, 'destroy']);
    Route::get('/angkatan-kelas/{id}/mahasiswa', [AngkatanKelasController::class, 'mahasiswa']);
    Route::get('/angkatan-kelas/{id}/mata-kuliah', [AngkatanKelasController::class, 'mataKuliah']);

    // --- CRUD Enrollment Mahasiswa Angkatan Kelas ---
    Route::get('/enrollment-kelas', [EnrollmentKelasController::class, 'index']);
    Route::post('/enrollment-kelas', [EnrollmentKelasController::class, 'store']);
    Route::get('/enrollment-kelas/{id}', [EnrollmentKelasController::class, 'show']);
    Route::put('/enrollment-kelas/{id}', [EnrollmentKelasController::class, 'update']);
    Route::delete('/enrollment-kelas/{id}', [EnrollmentKelasController::class, 'destroy']);

    // --- CRUD Status Mahasiswa ---
    Route::get('/status-mahasiswa', [StatusMahasiswaController::class, 'index']);
    Route::post('/status-mahasiswa', [StatusMahasiswaController::class, 'store']);
    Route::get('/status-mahasiswa/{id}', [StatusMahasiswaController::class, 'show']);
    Route::put('/status-mahasiswa/{id}', [StatusMahasiswaController::class, 'update']);
    Route::delete('/status-mahasiswa/{id}', [StatusMahasiswaController::class, 'destroy']);

    // --- Integrasi Dosen (Eksternal Tridharma) ---
    Route::get('/dosen', [DosenController::class, 'index']);

    // --- CRUD Enrollment Mata Kuliah ---
    Route::get('/enrollment-mata-kuliah', [EnrollmentMataKuliahController::class, 'index']);
    Route::post('/enrollment-mata-kuliah', [EnrollmentMataKuliahController::class, 'store']);
    Route::get('/enrollment-mata-kuliah/{id}', [EnrollmentMataKuliahController::class, 'show']);
    Route::put('/enrollment-mata-kuliah/{id}', [EnrollmentMataKuliahController::class, 'update']);
    Route::delete('/enrollment-mata-kuliah/{id}', [EnrollmentMataKuliahController::class, 'destroy']);

    // --- CRUD Mata Kuliah Angkatan Kelas ---
    Route::get('/mata-kuliah-angkatan-kelas', [MataKuliahAngkatanKelasController::class, 'index']);
    Route::post('/mata-kuliah-angkatan-kelas', [MataKuliahAngkatanKelasController::class, 'store']);
    Route::get('/mata-kuliah-angkatan-kelas/{id}', [MataKuliahAngkatanKelasController::class, 'show']);
    Route::put('/mata-kuliah-angkatan-kelas/{id}', [MataKuliahAngkatanKelasController::class, 'update']);
    Route::delete('/mata-kuliah-angkatan-kelas/{id}', [MataKuliahAngkatanKelasController::class, 'destroy']);

    // --- CRUD Prestasi Mahasiswa ---
    Route::get('/prestasi-mahasiswa', [PrestasiMahasiswaController::class, 'index']);
    Route::post('/prestasi-mahasiswa', [PrestasiMahasiswaController::class, 'store']);
    Route::get('/prestasi-mahasiswa/{id}', [PrestasiMahasiswaController::class, 'show']);
    Route::put('/prestasi-mahasiswa/{id}', [PrestasiMahasiswaController::class, 'update']);
    Route::delete('/prestasi-mahasiswa/{id}', [PrestasiMahasiswaController::class, 'destroy']);

    // --- CRUD Evidence Prestasi Mahasiswa ---
    Route::get('/evidence-prestasi-mahasiswa', [EvidencePrestasiMahasiswaController::class, 'index']);
    Route::post('/evidence-prestasi-mahasiswa', [EvidencePrestasiMahasiswaController::class, 'store']);
    Route::get('/evidence-prestasi-mahasiswa/{id}', [EvidencePrestasiMahasiswaController::class, 'show']);
    Route::put('/evidence-prestasi-mahasiswa/{id}', [EvidencePrestasiMahasiswaController::class, 'update']);
    Route::delete('/evidence-prestasi-mahasiswa/{id}', [EvidencePrestasiMahasiswaController::class, 'destroy']);

    // --- CRUD Jenis Prestasi ---
    Route::get('/jenis-prestasi', [JenisPrestasiController::class, 'index']);
    Route::post('/jenis-prestasi', [JenisPrestasiController::class, 'store']);
    Route::get('/jenis-prestasi/{id}', [JenisPrestasiController::class, 'show']);
    Route::put('/jenis-prestasi/{id}', [JenisPrestasiController::class, 'update']);
    Route::delete('/jenis-prestasi/{id}', [JenisPrestasiController::class, 'destroy']);

    // --- CRUD Data Nilai Mahasiswa ---
    Route::get('/nilai-mahasiswa', [NilaiMahasiswaController::class, 'index']);
    Route::get('/nilai-mahasiswa/template', [NilaiMahasiswaController::class, 'downloadTemplate']);
    Route::post('/nilai-mahasiswa/import', [NilaiMahasiswaController::class, 'importExcel']);
    Route::post('/nilai-mahasiswa', [NilaiMahasiswaController::class, 'store']);
    Route::post('/nilai-mahasiswa/batch', [NilaiMahasiswaController::class, 'batchStore']);
    Route::get('/nilai-mahasiswa/{id}', [NilaiMahasiswaController::class, 'show']);
    Route::put('/nilai-mahasiswa/{id}', [NilaiMahasiswaController::class, 'update']);
    Route::delete('/nilai-mahasiswa/{id}', [NilaiMahasiswaController::class, 'destroy']);
    Route::patch('/nilai-mahasiswa/{id}/link-nilai', [NilaiMahasiswaController::class, 'updateLinkNilai']);
    Route::patch('/nilai-mahasiswa/{id}/link-evidence', [NilaiMahasiswaController::class, 'updateLinkEvidence']);
    Route::get('/mahasiswa/{id}/khs', [NilaiMahasiswaController::class, 'khs']);
    Route::get('/mahasiswa/{id}/transkrip', [NilaiMahasiswaController::class, 'transkrip']);
    Route::get('/mahasiswa/{id}/ringkasan-akademik', [NilaiMahasiswaController::class, 'ringkasanAkademik']);
});
