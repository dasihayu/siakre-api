<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerguruanTinggiController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UppsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\CplController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\MataKuliahCplController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DataKelulusanController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');

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

    // --- CRUD Data Kelulusan ---
    Route::get('/data-kelulusan', [DataKelulusanController::class, 'index']);
    Route::post('/data-kelulusan', [DataKelulusanController::class, 'store']);
    Route::get('/data-kelulusan/{id}', [DataKelulusanController::class, 'show']);
    Route::put('/data-kelulusan/{id}', [DataKelulusanController::class, 'update']);
    Route::delete('/data-kelulusan/{id}', [DataKelulusanController::class, 'destroy']);
});
