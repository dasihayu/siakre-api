<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 22 Modul Entitas Sistem Akreditasi sesuai Dokumen Matriks Hak Akses
        $modules = [
            // Data Master Akademik
            'perguruan_tinggi',
            'prodi',
            'kelas',
            'upps',
            'anggota_upps',
            'mata_kuliah',
            'tahun_akademik',
            'angkatan',

            // Kurikulum & CPL
            'kurikulum',
            'cpl',
            'mata_kuliah_cpl',

            // Mahasiswa
            'mahasiswa',
            'status_mahasiswa',
            'kegiatan_mahasiswa',
            'nilai_mahasiswa',
            'prestasi_mahasiswa',
            'evidence_prestasi_mahasiswa',
            'jenis_prestasi',

            // Transaksional / Enrollment
            'angkatan_kelas',
            'enrollment_kelas',
            'enrollment_mata_kuliah',
            'mata_kuliah_angkatan_kelas',

            // Manajemen Hak Akses & Pengguna (Admin)
            'user',
            'role',
            'permission',
        ];

        // Actions CRUD
        $actions = ['create', 'read', 'update', 'delete'];

        // Create Permissions per Modul (Format: {module}.{action}, misal: perguruan_tinggi.read)
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$module}.{$action}", 'api');
            }
        }

        // Helper closures untuk membangun daftar permissions
        $crud = fn (string $module) => [
            "{$module}.create",
            "{$module}.read",
            "{$module}.update",
            "{$module}.delete",
        ];

        $readOnly = fn (string $module) => [
            "{$module}.read",
        ];

        $cru = fn (string $module) => [
            "{$module}.create",
            "{$module}.read",
            "{$module}.update",
        ];

        // 1. ADMINISTRATOR: Memegang kendali penuh (CRUD) ke seluruh sistem
        $adminRole = Role::findOrCreate(UserRole::ADMINISTRATOR->value, 'api');
        $adminRole->syncPermissions(Permission::all());

        // 2. OPERATOR: Administrasi data mahasiswa, kelas, enrollment (Bukan struktur Kurikulum/CPL)
        $operatorModulesCrud = [
            'kelas',
            'upps',
            'anggota_upps',
            'tahun_akademik',
            'angkatan',
            'mahasiswa',
            'status_mahasiswa',
            'kegiatan_mahasiswa',
            'prestasi_mahasiswa',
            'evidence_prestasi_mahasiswa',
            'jenis_prestasi',
            'angkatan_kelas',
            'enrollment_kelas',
            'enrollment_mata_kuliah',
            'mata_kuliah_angkatan_kelas',
        ];

        $operatorModulesRead = [
            'perguruan_tinggi',
            'prodi',
            'mata_kuliah',
            'kurikulum',
            'cpl',
            'mata_kuliah_cpl',
            'nilai_mahasiswa',
        ];

        $operatorPermissions = [];
        foreach ($operatorModulesCrud as $m) {
            $operatorPermissions = array_merge($operatorPermissions, $crud($m));
        }
        foreach ($operatorModulesRead as $m) {
            $operatorPermissions = array_merge($operatorPermissions, $readOnly($m));
        }

        $operatorRole = Role::findOrCreate(UserRole::OPERATOR->value, 'api');
        $operatorRole->syncPermissions($operatorPermissions);

        // 3. KAPRODI: Merancang standar akademik (CRUD Kurikulum, CPL, Mata Kuliah, Pivot Matkul-CPL)
        $kaprodiModulesCrud = [
            'mata_kuliah',
            'kurikulum',
            'cpl',
            'mata_kuliah_cpl',
        ];

        $kaprodiModulesRead = [
            'perguruan_tinggi',
            'prodi',
            'kelas',
            'upps',
            'anggota_upps',
            'tahun_akademik',
            'angkatan',
            'mahasiswa',
            'status_mahasiswa',
            'kegiatan_mahasiswa',
            'nilai_mahasiswa',
            'prestasi_mahasiswa',
            'evidence_prestasi_mahasiswa',
            'jenis_prestasi',
            'angkatan_kelas',
            'enrollment_kelas',
            'enrollment_mata_kuliah',
            'mata_kuliah_angkatan_kelas',
        ];

        $kaprodiPermissions = [];
        foreach ($kaprodiModulesCrud as $m) {
            $kaprodiPermissions = array_merge($kaprodiPermissions, $crud($m));
        }
        foreach ($kaprodiModulesRead as $m) {
            $kaprodiPermissions = array_merge($kaprodiPermissions, $readOnly($m));
        }

        $kaprodiRole = Role::findOrCreate(UserRole::KAPRODI->value, 'api');
        $kaprodiRole->syncPermissions($kaprodiPermissions);

        // 4. DOSEN: CRU pada Nilai Mahasiswa, Read-Only pada entitas lainnya
        $dosenModulesRead = [
            'perguruan_tinggi',
            'prodi',
            'kelas',
            'upps',
            'anggota_upps',
            'mata_kuliah',
            'tahun_akademik',
            'angkatan',
            'kurikulum',
            'cpl',
            'mata_kuliah_cpl',
            'mahasiswa',
            'status_mahasiswa',
            'kegiatan_mahasiswa',
            'prestasi_mahasiswa',
            'evidence_prestasi_mahasiswa',
            'jenis_prestasi',
            'angkatan_kelas',
            'enrollment_kelas',
            'enrollment_mata_kuliah',
            'mata_kuliah_angkatan_kelas',
        ];

        $dosenPermissions = $cru('nilai_mahasiswa');
        foreach ($dosenModulesRead as $m) {
            $dosenPermissions = array_merge($dosenPermissions, $readOnly($m));
        }

        $dosenRole = Role::findOrCreate(UserRole::DOSEN->value, 'api');
        $dosenRole->syncPermissions($dosenPermissions);

        // 5. REVIEWER: Akses murni Read-Only ke semua 22 modul sistem
        $reviewerModules = [
            'perguruan_tinggi',
            'prodi',
            'kelas',
            'upps',
            'anggota_upps',
            'mata_kuliah',
            'tahun_akademik',
            'angkatan',
            'kurikulum',
            'cpl',
            'mata_kuliah_cpl',
            'mahasiswa',
            'status_mahasiswa',
            'kegiatan_mahasiswa',
            'nilai_mahasiswa',
            'prestasi_mahasiswa',
            'evidence_prestasi_mahasiswa',
            'jenis_prestasi',
            'angkatan_kelas',
            'enrollment_kelas',
            'enrollment_mata_kuliah',
            'mata_kuliah_angkatan_kelas',
        ];

        $reviewerPermissions = [];
        foreach ($reviewerModules as $m) {
            $reviewerPermissions = array_merge($reviewerPermissions, $readOnly($m));
        }

        $reviewerRole = Role::findOrCreate(UserRole::REVIEWER->value, 'api');
        $reviewerRole->syncPermissions($reviewerPermissions);

        // 6. PIMPINAN: Read-Only global kecuali Pivot Mata Kuliah CPL (-)
        $pimpinanModules = [
            'perguruan_tinggi',
            'prodi',
            'kelas',
            'upps',
            'anggota_upps',
            'mata_kuliah',
            'tahun_akademik',
            'angkatan',
            'kurikulum',
            'cpl',
            // 'mata_kuliah_cpl' (-) Tidak punya akses sama sekali
            'mahasiswa',
            'status_mahasiswa',
            'kegiatan_mahasiswa',
            'nilai_mahasiswa',
            'prestasi_mahasiswa',
            'evidence_prestasi_mahasiswa',
            'jenis_prestasi',
            'angkatan_kelas',
            'enrollment_kelas',
            'enrollment_mata_kuliah',
            'mata_kuliah_angkatan_kelas',
        ];

        $pimpinanPermissions = [];
        foreach ($pimpinanModules as $m) {
            $pimpinanPermissions = array_merge($pimpinanPermissions, $readOnly($m));
        }

        $pimpinanRole = Role::findOrCreate(UserRole::PIMPINAN->value, 'api');
        $pimpinanRole->syncPermissions($pimpinanPermissions);
    }
}
