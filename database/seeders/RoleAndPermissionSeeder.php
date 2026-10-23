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

        // Bersihkan role usang jika ada di database
        Role::whereIn('name', ['OPERATOR', 'PIMPINAN'])->delete();

        // 1. Modul Entitas Sistem Akreditasi K1
        $k1Modules = [
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

            // Integrasi Eksternal
            'dosen',

            // Manajemen Hak Akses & Pengguna (Admin)
            'user',
            'role',
            'permission',
        ];

        // 2. Modul Integrasi Kelompok 2 (SDM, Tridharma, & Kerja Sama)
        $k2Modules = [
            'dosen',
            'kegiatan',
            'luaran',
            'kerjasama',
            'evidence',
            'penelitian',
            'pengabdian',
            'sertifikasi',
            'tendik',
            'mitra',
            'atribut',
        ];

        // Actions CRUD
        $actions = ['create', 'read', 'update', 'delete'];

        // Gabungkan seluruh modul K1 & K2 secara unik tanpa duplikasi
        $allCrudModules = array_values(array_unique(array_merge($k1Modules, $k2Modules)));

        // Create Permissions per Modul
        foreach ($allCrudModules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'api',
                ]);
            }
        }

        // Wildcard permissions untuk integrasi Kelompok 2 (misal: dosen.*)
        foreach ($k2Modules as $module) {
            Permission::firstOrCreate([
                'name' => "{$module}.*",
                'guard_name' => 'api',
            ]);
        }

        // Special permissions Kelompok 2
        $k2SpecialPermissions = [
            '*',
            'evidence.validate',
            'assessment.read',
            'assessment.run',
            'dashboard.read',
            'kriteria.read',
            'led.read',
            'lkps.read',
        ];

        foreach ($k2SpecialPermissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'api',
            ]);
        }

        // Reset cache Spatie agar permission baru terbaca
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

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

        // 1. ADMINISTRATOR: Memegang kendali penuh (CRUD) ke seluruh sistem K1 & K2
        $adminRole = Role::firstOrCreate(['name' => UserRole::ADMINISTRATOR->value, 'guard_name' => 'api']);
        $adminRole->syncPermissions(Permission::all());

        // 2. KAPRODI:
        // K1: Merancang standar akademik (CRUD Kurikulum, CPL, Mata Kuliah, Pivot Matkul-CPL; Read-Only lainnya)
        $kaprodiK1ModulesCrud = [
            'mata_kuliah',
            'kurikulum',
            'cpl',
            'mata_kuliah_cpl',
        ];

        $kaprodiK1ModulesRead = [
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
            'dosen',
        ];

        // K2: Sesuai Matriks SSO K2 (Tabel 3 No 3)
        $kaprodiK2Permissions = [
            'dosen.read',
            'kriteria.read',
            'penelitian.read',
            'pengabdian.read',
            'kegiatan.read',
            'luaran.read',
            'kerjasama.read',
            'sertifikasi.read',
            'evidence.read',
            'evidence.validate',
            'assessment.read',
            'assessment.run',
            'dashboard.read',
            'led.read',
            'lkps.read',
        ];

        $kaprodiPermissions = [];
        foreach ($kaprodiK1ModulesCrud as $m) {
            $kaprodiPermissions = array_merge($kaprodiPermissions, $crud($m));
        }
        foreach ($kaprodiK1ModulesRead as $m) {
            $kaprodiPermissions = array_merge($kaprodiPermissions, $readOnly($m));
        }
        $kaprodiPermissions = array_values(array_unique(array_merge($kaprodiPermissions, $kaprodiK2Permissions)));

        $kaprodiRole = Role::firstOrCreate(['name' => UserRole::KAPRODI->value, 'guard_name' => 'api']);
        $kaprodiRole->syncPermissions($kaprodiPermissions);

        // 3. DOSEN:
        // K1: CRU pada Nilai Mahasiswa, Read-Only pada entitas lainnya
        $dosenK1ModulesRead = [
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
            'dosen',
        ];

        // K2: Sesuai Matriks SSO K2 (Tabel 3 No 4)
        $dosenK2Permissions = [
            'dosen.read',
            'penelitian.create',
            'penelitian.read',
            'penelitian.update',
            'pengabdian.create',
            'pengabdian.read',
            'pengabdian.update',
            'kegiatan.create',
            'kegiatan.read',
            'kegiatan.update',
            'luaran.create',
            'luaran.read',
            'luaran.update',
            'sertifikasi.create',
            'sertifikasi.read',
            'sertifikasi.update',
            'evidence.create',
            'evidence.read',
            'evidence.update',
            'dashboard.read',
        ];

        $dosenPermissions = $cru('nilai_mahasiswa');
        foreach ($dosenK1ModulesRead as $m) {
            $dosenPermissions = array_merge($dosenPermissions, $readOnly($m));
        }
        $dosenPermissions = array_values(array_unique(array_merge($dosenPermissions, $dosenK2Permissions)));

        $dosenRole = Role::firstOrCreate(['name' => UserRole::DOSEN->value, 'guard_name' => 'api']);
        $dosenRole->syncPermissions($dosenPermissions);

        // 4. REVIEWER (Diakui sebagai ASESOR di K2):
        // K1: Akses murni Read-Only ke semua master data & data akademik K1
        $reviewerK1Modules = [
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
            'dosen',
        ];

        // K2: Sesuai Matriks SSO K2 (Tabel 3 No 5)
        $reviewerK2Permissions = [
            'dosen.read',
            'kriteria.read',
            'kegiatan.read',
            'luaran.read',
            'kerjasama.read',
            'evidence.read',
            'evidence.validate',
            'assessment.read',
            'assessment.run',
            'dashboard.read',
            'led.read',
            'lkps.read',
        ];

        $reviewerPermissions = [];
        foreach ($reviewerK1Modules as $m) {
            $reviewerPermissions = array_merge($reviewerPermissions, $readOnly($m));
        }
        $reviewerPermissions = array_values(array_unique(array_merge($reviewerPermissions, $reviewerK2Permissions)));

        $reviewerRole = Role::firstOrCreate(['name' => UserRole::REVIEWER->value, 'guard_name' => 'api']);
        $reviewerRole->syncPermissions($reviewerPermissions);

        // Reset cache permissions lagi di akhir
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
